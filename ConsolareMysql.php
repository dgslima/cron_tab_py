<?php
include_once 'Address.php';

class ConsolareMysql
{

    private $dns = "mysql:host=35.199.115.108;dbname=consolare_homolog";
    private $username = 'root';
    private $password = 'abc123**';
    private $conn;

    public function __construct()
    {
    }

    function connect()
    {
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
        $this->conn = new PDO($this->dns, $this->username, $this->password, $options);
    }

    function GetNotIncluded($where, $consolare_data)
    {
        $query = "SELECT txt_numero_nota, versao FROM guias_recolhimento gr WHERE gr.txt_numero_nota IN(" . implode(', ',array_map(function($num) {return "'" . $num . "'";}, $where)) . ") AND gr.hora_gravacao = (SELECT MAX(gr_inner.hora_gravacao) FROM guias_recolhimento gr_inner WHERE gr_inner.txt_numero_nota = gr.txt_numero_nota)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
            foreach ($results as $result) {
                foreach ($consolare_data as $index => $data) {
                    if ($data->dadosos_NumOrdem == $result['txt_numero_nota'] && $data->versao_1 == $result['versao']) {
                        unset($consolare_data[$index]);
                    }
                }
            }
        }
        return $consolare_data;
    }

    function checkAddress($address, $tipo)
    {
        // DOES NOT SELECT/INSERT IN DB IF IT DOES NOT EXIST
        if ($address->nome === null) {
            return ['lat' => null, 'lng' => null];
        }
        $formattedCEP = substr($address->cep, 0, 5) . '-' . substr($address->cep, 5); // xxxxx-xxx
        $numero = str_replace(' ', '', $address->numero);
        $bairro = $address->bairro??"";
        $query = "SELECT DISTINCT latitude, longitude FROM cadastro_enderecos WHERE cep = :cep and numero = :numero";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cep', $formattedCEP);
        $stmt->bindParam(':numero', $numero);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result && !empty($result["latitude"]) && !empty($result["longitude"])) {
            return ["lat" => $result["latitude"], "lng" => $result["longitude"]];
        }
        // IF NO RESULT OR LAT OR LNG IS EMPTY, GEODECODE AND INSERT IN DB
        $positions = $address->geocoding();
        $user = "silvio@fvblocadora.com.br";
        $query = "INSERT INTO cadastro_enderecos(
                                tipo_local,
                                usuario,
                                descricao,
                                endereco,
                                cep,
                                complemento,
                                bairro,
                                numero,
                                latitude,
                                longitude,
                                data_hora
                                )
                                VALUES(
                                       :tipo_local,
                                       :usuario,
                                       :descricao,
                                       :endereco,
                                       :cep,
                                       :complemento,
                                       :bairro,
                                       :numero,
                                       :latitude,
                                       :longitude,
                                       DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')
                                 )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo_local', $tipo);
        $stmt->bindParam(':usuario', $user);
        $stmt->bindParam(':descricao', $address->nome);
        $stmt->bindParam(':endereco', $address->logradouro);
        $stmt->bindParam(':cep', $formattedCEP);
        $stmt->bindParam(':complemento', $address->complemento);
        $stmt->bindParam(':bairro', $bairro);
        $stmt->bindParam(':numero', $address->numero);
        $stmt->bindParam(':latitude', $positions['lat'], PDO::PARAM_STR);
        $stmt->bindParam(':longitude', $positions['lng'], PDO::PARAM_STR);
        $stmt->execute();
        return $positions;
    }

    function checkUrna($urna)
    {
        if ($urna) { // Urna exist on Guia check if exist on DB
            $query = "(SELECT id FROM urnas WHERE descricao = :nome_urna)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nome_urna', $urna->name);
            $stmt->execute();
            $result = $stmt->fetchColumn();
            if ($result) { // IF exist return ID
                return $result;
            }
            // if not Insert Urna in the DB and return the ID
            $query = "INSERT INTO urnas(codigo, descricao) VALUES(:codigo, :nome_urna)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':codigo', $urna->id);
            $stmt->bindParam(':nome_urna', $urna->name);
            $stmt->execute();
            return $this->conn->lastInsertId()[0];
        }
        return null;
    }

    function GenerateFamilyUrl()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // String of all capital letters
        $charactersLength = strlen($characters);
        do { // Create a new code while this code exist in the DB
            $randomString = '';
            for ($i = 0; $i < 12; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            $query = '(SELECT code FROM guias_recolhimento WHERE code = :code)';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':code', $randomString);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                continue;
            }
            break;
        } while (true);
        return $randomString;
    }

    function insert_guia($consolare)
    {
        $falacimento_datetime = substr($consolare->falecido_DataFalecimento, 0, 10) . ' ' . $consolare->falecido_HoraFalecimento ?? '00:00' . ':00'; // YYYY-MM-DD HH:mm:ss
        $agencia = '%' . trim(str_replace(array("AGENCIA", "-"), "", $consolare->cabecalho_NomeFuneraria)); // ADD % TO GET LIKE, AND REMOVE UNNECESSARY VALUES FROM STR

        // Format the number 11 12345 1234 TO (xx) xxxxx-xxxx
        $numbers_fone = preg_replace('/\D/', '', $consolare->contratante_Fone1); // Get only digits
        $tel = "(".substr($numbers_fone, 0, 2).")".substr($numbers_fone, 2, 5)."-".substr($numbers_fone, 7, 4); //(xx) xxxxx-xxxx

        // TRANSFORMING DATE AND TIME
        $vel_date = substr($consolare->remocao_VelorioData, 0, 10); // YYYY-MM-DD
        $sep_date = substr($consolare->sepultamento_Data, 0, 10);  // YYYY-MM-DD
        $vel_hr = substr($consolare->remocao_VelorioHora, 0, 2) . ':' . substr($consolare->remocao_VelorioHora, 2, 2); // HH:mm
        $sep_hr = substr($consolare->sepultamento_Hora, 0, 2) . ':' . substr($consolare->sepultamento_Hora, 2, 2); // HH:mm

        // Generate family_url code with 12 random char
        $code = $this->GenerateFamilyUrl();
        $family_url = "https://consolare.ossbrasil.com/view/auth.php?code=$code";

        // Check Urna
        $id = $this->checkUrna($consolare->findItemWithUrna());

        // Check Addresses
        $hospital_address = new Address(
            $consolare->remocao_RemocaoNome,
            $consolare->remocao_RemocaoCEP,
            $consolare->remocao_RemocaoLogradouro,
            $consolare->remocao_RemocaoNumero,
            $consolare->remocao_RemocaoBairro,
            $consolare->remocao_RemocaoComplemento,
            $consolare->remocao_RemocaoCidade,
            $consolare->remocao_RemocaoUF
        );
        $hospital_lat_lng = $this->checkAddress($hospital_address, "3");

        $clinica_address = new Address(
            $consolare->remocao_LaboratorioNome,
            $consolare->remocao_LaboratorioCEP,
            $consolare->remocao_LaboratorioLogradouro,
            $consolare->remocao_LaboratorioNumero,
            $consolare->remocao_LaboratorioBairro,
            $consolare->remocao_LaboratorioComplemento,
            $consolare->remocao_LaboratorioCidade,
            $consolare->remocao_LaboratorioUF,
        );
        $clinica_lat_lng = $this->checkAddress($clinica_address, "4");

        $funeral_address = new Address(
            $consolare->remocao_VelorioNome,
            $consolare->remocao_VelorioCEP,
            $consolare->remocao_VelorioLogradouro,
            $consolare->remocao_VelorioNumero,
            $consolare->remocao_VelorioBairro,
            $consolare->remocao_VelorioComplemento,
            $consolare->remocao_VelorioCidade,
            $consolare->remocao_VelorioUF,
        );

        $funeral_lat_lng = $this->checkAddress($funeral_address, "5");

        $cemiterio_address = new Address(
            $consolare->sepultamento_Nome,
            $consolare->sepultamento_Cep,
            $consolare->sepultamento_Logradouro,
            $consolare->sepultamento_Numero,
            $consolare->sepultamento_Bairro,
            $consolare->sepultamento_Complemento,
            $consolare->sepultamento_Cidade,
            $consolare->sepultamento_UF,
        );

        $cemiterio_lat_lng = $this->checkAddress($cemiterio_address, "6");

        $stmt = $this->conn->prepare("INSERT INTO guias_recolhimento ( 
                                            flag,
                                            usuario_cadastro,
                                            hora_gravacao,
                                            id_contratacao,
                                            txt_numero_nota,
                                            nome_falecido,
                                            data_falecimento,
                                            code,
                                            url_familia,
                                            id_agencia, telefone,
                                            id_urna, desc_remocao,
                                            end_remocao_sel,
                                            rot_remocao_lat,
                                            rot_remocao_long,
                                            desc_clinica,
                                            end_clinica_sel,
                                            rot_clinica_lat,
                                            rot_clinica_long,
                                            desc_velorio,
                                            end_velorio_sel,
                                            rot_velo_lat,
                                            rot_velo_long,
                                            desc_cemiterio,
                                            end_cemiterio_sel,
                                            rot_cemi_lat,
                                            rot_cemi_long,
                                            data_saida_velorio,
                                            hora_saida_velorio,
                                            data_sepultamento_cremacao,
                                            hr_sepultamento_cremacao,
                                            tipo_contratacao,
                                            duracao_velorio,
                                            data_hora_contratacao,
                                            tarifas,
                                            obs,
                                            versao) 
                                VALUES ( 
                                        '1',
                                        'SisFuner',
                                        DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'),
                                        :id_contratacao,
                                        :txt_numero_nota,
                                        :nome_falecido,
                                        :data_falecimento, 
                                        :code,
                                        :family_url,
                                        (SELECT id FROM agencias WHERE nome LIKE :nome_agencia COLLATE utf8mb4_unicode_ci),
                                        :telefone, 
                                        :id_urna,
                                        :hosp_nome,
                                        :hosp_end,
                                        :hosp_latitude,
                                        :hosp_longitude,
                                        :lab_nome,
                                        :lab_end,
                                        :lab_latitude,
                                        :lab_longitude,
                                        :fun_nome,
                                        :fun_end,
                                        :fun_latitude,
                                        :fun_longitude,
                                        :cem_nome,
                                        :cem_end,
                                        :cem_latitude,
                                        :cem_longitude,
                                        :vel_date,
                                        :vel_time,
                                        :sep_date,
                                        :sep_time,
                                        :tipo_contratacao,
                                        :tipo_contratacao_spregula,
                                        :duracao_velorio,
                                        :data_hora_contratacao,
                                        :tarifas,
                                        :obs,
                                        :ver)
                                        ON DUPLICATE KEY  UPDATE 
    
                                            data_retirada=DATE_FORMAT(NOW(), '%Y-%m-%d'),
                                            nome_falecido=:nome_falecido,
                                            data_falecimento=:data_falecimento,
                                            code=:code,
                                            url_familia=:family_url,
                                            id_agencia=(SELECT id FROM agencias WHERE nome LIKE :nome_agencia COLLATE utf8mb4_unicode_ci), 
                                            telefone=:telefone,
                                            id_urna=:id_urna, 
                                            desc_remocao=:hosp_nome,
                                            end_remocao_sel=:hosp_end,
                                            rot_remocao_lat=:hosp_latitude,
                                            rot_remocao_long=:hosp_longitude,
                                            desc_clinica=:lab_nome,
                                            end_clinica_sel=:lab_end,
                                            rot_clinica_lat=:lab_latitude,
                                            rot_clinica_long=:lab_longitude,
                                            desc_velorio=:fun_nome,
                                            end_velorio_sel=:fun_end,
                                            rot_velo_lat=:fun_latitude,
                                            rot_velo_long=:fun_longitude,
                                            desc_cemiterio=:cem_nome,
                                            end_cemiterio_sel=:cem_end,
                                            rot_cemi_lat=:cem_latitude,
                                            rot_cemi_long=:cem_longitude,
                                            data_saida_velorio=:vel_date,
                                            hora_saida_velorio=:vel_time,
                                            data_sepultamento_cremacao=:sep_date,
                                            hr_sepultamento_cremacao=:sep_time,
                                            tipo_contratacao=:tipo_contratacao,
                                            tipo_contratacao_spregula=:tipo_contratacao_spregula,
                                            duracao_velorio=:duracao_velorio,
                                            data_hora_contratacao=:data_hora_contratacao,
                                            tarifas=:tarifas,
                                            obs=:obs,
                                            versao=:ver
                                        ");
        $stmt->bindParam(':id_contratacao', $consolare->falecido_RegistroPROAIM);
        $stmt->bindParam(':txt_numero_nota', $consolare->dadosos_NumOrdem);
        $stmt->bindParam(':nome_falecido', $consolare->falecido_Nome);
        $stmt->bindParam(':data_falecimento', $falacimento_datetime);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':family_url', $family_url);
        $stmt->bindParam(':nome_agencia', $agencia);
        $stmt->bindParam(':telefone', $tel);
        $stmt->bindParam(':id_urna', $id, PDO::PARAM_STR);

        // Hospital params
        $hospital_string = $hospital_address->toString();
        $stmt->bindParam(':hosp_nome', $hospital_address->nome);
        $stmt->bindParam(':hosp_end', $hospital_string);
        $stmt->bindParam(':hosp_latitude', $hospital_lat_lng['lat'], PDO::PARAM_STR);
        $stmt->bindParam(':hosp_longitude', $hospital_lat_lng['lng'], PDO::PARAM_STR);

        // Clinica params
        $clinica_string = $clinica_address->toString();
        $stmt->bindParam(':lab_nome', $clinica_address->nome);
        $stmt->bindParam(':lab_end', $clinica_string);
        $stmt->bindParam(':lab_latitude', $clinica_lat_lng['lat'], PDO::PARAM_STR);
        $stmt->bindParam(':lab_longitude', $clinica_lat_lng['lng'], PDO::PARAM_STR);

        // Funeral params
        $funeral_string = $funeral_address->toString();
        $stmt->bindParam(':fun_nome', $funeral_address->nome);
        $stmt->bindParam(':fun_end', $funeral_string);
        $stmt->bindParam(':fun_latitude', $funeral_lat_lng['lat'], PDO::PARAM_STR);
        $stmt->bindParam(':fun_longitude', $funeral_lat_lng['lng'], PDO::PARAM_STR);

        // Cemiterio params
        $cemiterio_string = $cemiterio_address->toString();
        $stmt->bindParam(':cem_nome', $cemiterio_address->nome);
        $stmt->bindParam(':cem_end', $cemiterio_string);
        $stmt->bindParam(':cem_latitude', $cemiterio_lat_lng['lat'], PDO::PARAM_STR);
        $stmt->bindParam(':cem_longitude', $cemiterio_lat_lng['lng'], PDO::PARAM_STR);


        // DATETIME
        $stmt->bindParam(':vel_date', $vel_date);
        $stmt->bindParam(':vel_time', $vel_hr);
        $stmt->bindParam(':sep_date', $sep_date);
        $stmt->bindParam(':sep_time', $sep_hr);

        // OBS AND VERSION
        // New fields mapping
        $stmt->bindParam(':tipo_contratacao', $consolare->dadosos_TipoDeContratacao);
        $stmt->bindParam(':tipo_contratacao_spregula', $consolare->dadosos_TipoDeContratacao_SPRegula);
        $stmt->bindParam(':duracao_velorio', $consolare->remocao_VelorioDuracao);
        $stmt->bindParam(':data_hora_contratacao', $consolare->dadosos_DataEmissao);
        $stmt->bindParam(':tarifas', $consolare->itens_ValorTotal);
        $stmt->bindParam(':obs', $consolare->itens_Observacoes);
        $stmt->bindParam(':ver', $consolare->versao_1);
        $stmt->execute();
    }
}