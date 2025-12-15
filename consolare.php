<?php

class Consolare
{
    public $cabecalho_1;
    public $cabecalho_2;
    public $cabecalho_3;
    public $cabecalho_4;
    public $cabecalho_NomeFuneraria;
    public $cabecalho_Endereco;
    public $cabecalho_Bairro;
    public $cabecalho_CNPJ;
    public $dadosos_1;
    public $dadosos_2;
    public $dadosos_3;
    public $dadosos_4;
    public $dadosos_5;
    public $dadosos_6;
    public $dadosos_7;
    public $dadosos_8;
    public $dadosos_NumOrdem;
    public $dadosos_Funeraria;
    public $dadosos_NotaFiscal;
    public $dadosos_DataInsert;
    public $dadosos_DataUpdate;
    public $dadosos_TipoDeContratacao;
    public $dadosos_DataEmissao;
    public $dadosos_Agencia;
    public $dadosos_Tel;
    public $dadosos_EnviadaParaPolo;
    public $dadosos_EnviadaParaPolo_Nome;
    public $contratante_1;
    public $contratante_2;
    public $contratante_3;
    public $contratante_4;
    public $contratante_5;
    public $contratante_6;
    public $contratante_7;
    public $contratante_8;
    public $contratante_9;
    public $contratante_Contratante;
    public $contratante_GrauDeParentesco;
    public $contratante_NomeSocial;
    public $contratante_CPF;
    public $contratante_RG;
    public $contratante_Fone1;
    public $contratante_Fone2;
    public $contratante_Email;
    public $contratante_Endereco;
    public $contratante_EnderecoNumero;
    public $contratante_EnderecoComplemento;
    public $contratante_Bairro;
    public $contratante_Cidade;
    public $contratante_UF;
    public $contratante_Cep;
    public $falecido_1;
    public $falecido_2;
    public $falecido_3;
    public $falecido_4;
    public $falecido_5;
    public $falecido_6;
    public $falecido_7;
    public $falecido_8;
    public $falecido_9;
    public $falecido_10;
    public $falecido_11;
    public $falecido_Nome;
    public $falecido_Peso;
    public $falecido_Altura;
    public $falecido_NomeSocial;
    public $falecido_Sexo;
    public $falecido_Mae;
    public $falecido_DataNascimento;
    public $falecido_DataFalecimento;
    public $falecido_HoraFalecimento;
    public $falecido_RegistroPROAIM;
    public $falecido_CausaMorte1;
    public $falecido_CausaMorte2;
    public $falecido_CausaMorte3;
    public $falecido_CausaMorte4;
    public $remocao_1;
    public $remocao_2;
    public $remocao_3;
    public $remocao_4;
    public $remocao_5;
    public $remocao_6;
    public $remocao_7;
    public $remocao_8;
    public $remocao_9;
    public $remocao_RemocaoNome;
    public $remocao_RemocaoLogradouro;
    public $remocao_RemocaoNumero;
    public $remocao_RemocaoComplemento;
    public $remocao_RemocaoBairro;
    public $remocao_RemocaoCidade;
    public $remocao_RemocaoUF;
    public $remocao_RemocaoCEP;
    public $remocao_RemocaoData;
    public $remocao_RemocaoHora;
    public $remocao_LaboratorioNome;
    public $remocao_LaboratorioLogradouro;
    public $remocao_LaboratorioNumero;
    public $remocao_LaboratorioComplemento;
    public $remocao_LaboratorioBairro;
    public $remocao_LaboratorioCidade;
    public $remocao_LaboratorioUF;
    public $remocao_LaboratorioCEP;
    public $remocao_VelorioNome;
    public $remocao_VelorioLogradouro;
    public $remocao_VelorioNumero;
    public $remocao_VelorioComplemento;
    public $remocao_VelorioBairro;
    public $remocao_VelorioCidade;
    public $remocao_VelorioUF;
    public $remocao_VelorioCEP;
    public $remocao_VelorioData;
    public $remocao_VelorioHora;
    public $remocao_VelorioDuracao;
    public $sepultamento_1;
    public $sepultamento_2;
    public $sepultamento_3;
    public $sepultamento_4;
    public $sepultamento_5;
    public $sepultamento_SeCremacao;
    public $sepultamento_Nome;
    public $sepultamento_Logradouro;
    public $sepultamento_Numero;
    public $sepultamento_Complemento;
    public $sepultamento_Bairro;
    public $sepultamento_Cidade;
    public $sepultamento_UF;
    public $sepultamento_Cep;
    public $sepultamento_Data;
    public $sepultamento_Hora;
    public $itens_1;

    /**
     * @var Item[]
     */
    public $itens_2;
    public $itens_3;
    public $itens_4;
    public $itens_NumOrdem;
    public $itens_Funeraria;
    public $itens_Observacoes;
    public $itens_ValorTotal;
    public $pagamento_1;
    public $pagamento_2;
    public $pagamento_3;
    public $pagamento_4;
    public $pagamento_5;
    public $pagamento_Situacao;
    public $pagamento_TotalPago;
    public $versao_1;
    public $versao_Numero;
    public $responsavel_1;
    public $responsavel_2;
    public $responsavel_3;
    public $responsavel_Nome;
    public $responsavel_Documento;
    public $atendente_1;
    public $atendente_2;
    public $atendente_3;
    public $atendente_4;
    public $atendente_Codigo;
    public $atendente_Nome;
    public $atendente_NomeContratante;
    public $nota_Fiscal;
    public $nota_Fiscal_URL;
    public $nota_Fiscal_Data;

    // Constructor to initialize properties from JSON data
    public function __construct($json)
    {
        $this->cabecalho_1 = $json['_CABECALHO_1'];
        $this->cabecalho_2 = $json['_CABECALHO_2'];
        $this->cabecalho_3 = $json['_CABECALHO_3'];
        $this->cabecalho_4 = $json['_CABECALHO_4'];
        $this->cabecalho_NomeFuneraria = $json['_CABECALHO_NomeFuneraria'];
        $this->cabecalho_Endereco = $json['_CABECALHO_Endereco'];
        $this->cabecalho_Bairro = $json['_CABECALHO_Bairro'];
        $this->cabecalho_CNPJ = $json['_CABECALHO_CNPJ'];
        $this->dadosos_1 = $json['_DADOSOS_1'];
        $this->dadosos_2 = $json['_DADOSOS_2'];
        $this->dadosos_3 = $json['_DADOSOS_3'];
        $this->dadosos_4 = $json['_DADOSOS_4'];
        $this->dadosos_5 = $json['_DADOSOS_5'];
        $this->dadosos_6 = $json['_DADOSOS_6'];
        $this->dadosos_7 = $json['_DADOSOS_7'];
        $this->dadosos_8 = $json['_DADOSOS_8'];
        $this->dadosos_NumOrdem = $json['_DADOSOS_NumOrdem'];
        $this->dadosos_Funeraria = $json['_DADOSOS_Funeraria'];
        $this->dadosos_NotaFiscal = $json['_DADOSOS_NotaFiscal'];
        $this->dadosos_DataInsert = $json['_DADOSOS_DataInsert'];
        $this->dadosos_DataUpdate = $json['_DADOSOS_DataUpdate'];
        $this->dadosos_TipoDeContratacao = $json['_DADOSOS_TipoDeContratacao'];
        $this->dadosos_DataEmissao = $json['_DADOSOS_DataEmissao'];
        $this->dadosos_Agencia = $json['_DADOSOS_Agencia'];
        $this->dadosos_Tel = $json['_DADOSOS_Tel'];
        $this->dadosos_EnviadaParaPolo = $json['_DADOSOS_EnviadaParaPolo'];
        $this->dadosos_EnviadaParaPolo_Nome = $json['_DADOSOS_EnviadaParaPolo_Nome'];
        $this->contratante_1 = $json['_CONTRATANTE_1'];
        $this->contratante_2 = $json['_CONTRATANTE_2'];
        $this->contratante_3 = $json['_CONTRATANTE_3'];
        $this->contratante_4 = $json['_CONTRATANTE_4'];
        $this->contratante_5 = $json['_CONTRATANTE_5'];
        $this->contratante_6 = $json['_CONTRATANTE_6'];
        $this->contratante_7 = $json['_CONTRATANTE_7'];
        $this->contratante_8 = $json['_CONTRATANTE_8'];
        $this->contratante_9 = $json['_CONTRATANTE_9'];
        $this->contratante_Contratante = $json['_CONTRATANTE_Contratante'];
        $this->contratante_GrauDeParentesco = $json['_CONTRATANTE_GrauDeParentesco'];
        $this->contratante_NomeSocial = $json['_CONTRATANTE_NomeSocial'];
        $this->contratante_CPF = $json['_CONTRATANTE_CPF'];
        $this->contratante_RG = $json['_CONTRATANTE_RG'];
        $this->contratante_Fone1 = $json['_CONTRATANTE_Fone1'];
        $this->contratante_Fone2 = $json['_CONTRATANTE_Fone2'];
        $this->contratante_Email = $json['_CONTRATANTE_Email'];
        $this->contratante_Endereco = $json['_CONTRATANTE_Endereco'];
        $this->contratante_EnderecoNumero = $json['_CONTRATANTE_EnderecoNumero'];
        $this->contratante_EnderecoComplemento = $json['_CONTRATANTE_EnderecoComplemento'];
        $this->contratante_Bairro = $json['_CONTRATANTE_Bairro'];
        $this->contratante_Cidade = $json['_CONTRATANTE_Cidade'];
        $this->contratante_UF = $json['_CONTRATANTE_UF'];
        $this->contratante_Cep = $json['_CONTRATANTE_Cep'];
        $this->falecido_1 = $json['_FALECIDO_1'];
        $this->falecido_2 = $json['_FALECIDO_2'];
        $this->falecido_3 = $json['_FALECIDO_3'];
        $this->falecido_4 = $json['_FALECIDO_4'];
        $this->falecido_5 = $json['_FALECIDO_5'];
        $this->falecido_6 = $json['_FALECIDO_6'];
        $this->falecido_7 = $json['_FALECIDO_7'];
        $this->falecido_8 = $json['_FALECIDO_8'];
        $this->falecido_9 = $json['_FALECIDO_9'];
        $this->falecido_10 = $json['_FALECIDO_10'];
        $this->falecido_11 = $json['_FALECIDO_11'];
        $this->falecido_Nome = $json['_FALECIDO_Nome'];
        $this->falecido_Peso = $json['_FALECIDO_Peso'];
        $this->falecido_Altura = $json['_FALECIDO_Altura'];
        $this->falecido_NomeSocial = $json['_FALECIDO_NomeSocial'];
        $this->falecido_Sexo = $json['_FALECIDO_Sexo'];
        $this->falecido_Mae = $json['_FALECIDO_Mae'];
        $this->falecido_DataNascimento = $json['_FALECIDO_DataNascimento'];
        $this->falecido_DataFalecimento = $json['_FALECIDO_DataFalecimento'];
        $this->falecido_HoraFalecimento = $json['_FALECIDO_HoraFalecimento'];
        $this->falecido_RegistroPROAIM = $json['_FALECIDO_RegistroPROAIM'];
        $this->falecido_CausaMorte1 = $json['_FALECIDO_CausaMorte1'];
        $this->falecido_CausaMorte2 = $json['_FALECIDO_CausaMorte2'];
        $this->falecido_CausaMorte3 = $json['_FALECIDO_CausaMorte3'];
        $this->falecido_CausaMorte4 = $json['_FALECIDO_CausaMorte4'];
        $this->remocao_1 = $json['_REMOCAO_1'];
        $this->remocao_2 = $json['_REMOCAO_2'];
        $this->remocao_3 = $json['_REMOCAO_3'];
        $this->remocao_4 = $json['_REMOCAO_4'];
        $this->remocao_5 = $json['_REMOCAO_5'];
        $this->remocao_6 = $json['_REMOCAO_6'];
        $this->remocao_7 = $json['_REMOCAO_7'];
        $this->remocao_8 = $json['_REMOCAO_8'];
        $this->remocao_9 = $json['_REMOCAO_9'];
        $this->remocao_RemocaoNome = $json['_REMOCAO_RemocaoNome'];
        $this->remocao_RemocaoLogradouro = $json['_REMOCAO_RemocaoLogradouro'];
        $this->remocao_RemocaoNumero = $json['_REMOCAO_RemocaoNumero'];
        $this->remocao_RemocaoComplemento = $json['_REMOCAO_RemocaoComplemento'];
        $this->remocao_RemocaoBairro = $json['_REMOCAO_RemocaoBairro'];
        $this->remocao_RemocaoCidade = $json['_REMOCAO_RemocaoCidade'];
        $this->remocao_RemocaoUF = $json['_REMOCAO_RemocaoUF'];
        $this->remocao_RemocaoCEP = $json['_REMOCAO_RemocaoCEP'];
        $this->remocao_RemocaoData = $json['_REMOCAO_RemocaoData'];
        $this->remocao_RemocaoHora = $json['_REMOCAO_RemocaoHora'];
        $this->remocao_LaboratorioNome = $json['_REMOCAO_LaboratorioNome'];
        $this->remocao_LaboratorioLogradouro = $json['_REMOCAO_LaboratorioLogradouro'];
        $this->remocao_LaboratorioNumero = $json['_REMOCAO_LaboratorioNumero'];
        $this->remocao_LaboratorioComplemento = $json['_REMOCAO_LaboratorioComplemento'];
        $this->remocao_LaboratorioBairro = $json['_REMOCAO_LaboratorioBairro'];
        $this->remocao_LaboratorioCidade = $json['_REMOCAO_LaboratorioCidade'];
        $this->remocao_LaboratorioUF = $json['_REMOCAO_LaboratorioUF'];
        $this->remocao_LaboratorioCEP = $json['_REMOCAO_LaboratorioCEP'];
        $this->remocao_VelorioNome = $json['_REMOCAO_VelorioNome'];
        $this->remocao_VelorioLogradouro = $json['_REMOCAO_VelorioLogradouro'];
        $this->remocao_VelorioNumero = $json['_REMOCAO_VelorioNumero'];
        $this->remocao_VelorioComplemento = $json['_REMOCAO_VelorioComplemento'];
        $this->remocao_VelorioBairro = $json['_REMOCAO_VelorioBairro'];
        $this->remocao_VelorioCidade = $json['_REMOCAO_VelorioCidade'];
        $this->remocao_VelorioUF = $json['_REMOCAO_VelorioUF'];
        $this->remocao_VelorioCEP = $json['_REMOCAO_VelorioCEP'];
        $this->remocao_VelorioData = $json['_REMOCAO_VelorioData'];
        $this->remocao_VelorioHora = $json['_REMOCAO_VelorioHora'];
        $this->remocao_VelorioDuracao = $json['_REMOCAO_VelorioDuracao'];
        $this->sepultamento_1 = $json['_SEPULTAMENTO_1'];
        $this->sepultamento_2 = $json['_SEPULTAMENTO_2'];
        $this->sepultamento_3 = $json['_SEPULTAMENTO_3'];
        $this->sepultamento_4 = $json['_SEPULTAMENTO_4'];
        $this->sepultamento_5 = $json['_SEPULTAMENTO_5'];
        $this->sepultamento_Nome = $json['_SEPULTAMENTO_Nome'];
        $this->sepultamento_SeCremacao = $json['_SEPULTAMENTO_SeCremacao'];
        $this->sepultamento_Logradouro = $json['_SEPULTAMENTO_Logradouro'];
        $this->sepultamento_Numero = $json['_SEPULTAMENTO_Numero'];
        $this->sepultamento_Complemento = $json['_SEPULTAMENTO_Complemento'];
        $this->sepultamento_Bairro = $json['_SEPULTAMENTO_Bairro'];
        $this->sepultamento_Cidade = $json['_SEPULTAMENTO_Cidade'];
        $this->sepultamento_UF = $json['_SEPULTAMENTO_UF'];
        $this->sepultamento_Cep = $json['_SEPULTAMENTO_Cep'];
        $this->sepultamento_Data = $json['_SEPULTAMENTO_Data'];
        $this->sepultamento_Hora = $json['_SEPULTAMENTO_Hora'];
        $this->itens_1 = $json['_ITENS_1'];
        $this->itens_2 = $this->parseItems($json['_ITENS_2']);
        $this->itens_3 = $json['_ITENS_3'];
        $this->itens_4 = $json['_ITENS_4'];
        $this->itens_NumOrdem = $json['_ITENS_NumOrdem'];
        $this->itens_Funeraria = $json['_ITENS_Funeraria'];
        $this->itens_Observacoes = $json['_ITENS_Observacoes'];
        $this->itens_ValorTotal = $json['_ITENS_ValorTotal'];
        $this->pagamento_1 = $json['_PAGAMENTO_1'];
        $this->pagamento_2 = $json['_PAGAMENTO_2'];
        $this->pagamento_3 = $json['_PAGAMENTO_3'];
        $this->pagamento_4 = $json['_PAGAMENTO_4'];
        $this->pagamento_5 = $json['_PAGAMENTO_5'];
        $this->pagamento_Situacao = $json['_PAGAMENTO_Situacao'];
        $this->pagamento_TotalPago = $json['_PAGAMENTO_TotalPago'];
        $this->versao_1 = $json['_VERSAO_1'];
        $this->versao_Numero = $json['_VERSAO_Numero'];
        $this->responsavel_1 = $json['_RESPONSAVEL_1'];
        $this->responsavel_2 = $json['_RESPONSAVEL_2'];
        $this->responsavel_3 = $json['_RESPONSAVEL_3'];
        $this->responsavel_Nome = $json['_RESPONSAVEL_Nome'];
        $this->responsavel_Documento = $json['_RESPONSAVEL_Documento'];
        $this->atendente_1 = $json['_ATENDENTE_1'];
        $this->atendente_2 = $json['_ATENDENTE_2'];
        $this->atendente_3 = $json['_ATENDENTE_3'];
        $this->atendente_4 = $json['_ATENDENTE_4'];
        $this->atendente_Codigo = $json['_ATENDENTE_Codigo'];
        $this->atendente_Nome = $json['_ATENDENTE_Nome'];
        $this->atendente_NomeContratante = $json['_ATENDENTE_NomeContratante'];
        $this->nota_Fiscal = $json['NOTA_FISCAL'];
        $this->nota_Fiscal_URL = $json['NOTA_FISCAL_URL'];
        $this->nota_Fiscal_Data = $json['NOTA_FISCAL_DATA'];
    }

    function parseItems($itemsString)
    {
        // Define the regex pattern to match each item
        $pattern = '/(\d+)#([^#]*)#([^#]*)#(\d+)#([A-Z]+)#([\d.]+)(?:;|$)/';

        // Initialize an array to hold the Item objects
        $items = [];

        // Use preg_match_all to find all matches in the input string
        preg_match_all($pattern, $itemsString, $matches, PREG_SET_ORDER);

        // Loop through the matches and create Item objects
        foreach ($matches as $match) {
            $id = $match[1];
            $name = $match[2];
            $size = $match[3];
            $quantity = $match[4];
            $measurement = $match[5];
            $value = $match[6];

            $items[] = new Item($id, $name, $size, $quantity, $measurement, $value);
        }

        return $items;
    }

    function makeAdrress($cep, $logradouro, $numero, $complemento, $bairro)
    {
        $formattedCEP = str_pad($cep, 8, '0', STR_PAD_LEFT);
        $formattedCEP = substr($formattedCEP, 0, 5) . '-' . substr($formattedCEP, 5);
        $address = strtoupper($logradouro) . ' - Nº ' . trim($numero);
        if (!empty($complemento)) {
            $address .= ' - ' . strtoupper($complemento);
        }
        $address .= ' - ' . strtoupper($bairro) . ' - ' . $formattedCEP;
        return $address;
    }

    /**
     * Find an item name that contains the substring "URNA".
     *
     * @return Item|null
     */
    public function findItemWithUrna()
    {
        foreach ($this->itens_2 as $item) {
            if (stripos($item->name, 'URNA') !== false) {
                return $item;
            }
        }
        return null;
    }
}

class Item
{
    public $id;
    public $name;
    public $size;
    public $quantity;
    public $measurement;
    public $value;


    public function __construct($id, $name, $size, $quantity, $measurement, $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->size = $size;
        $this->quantity = $quantity;
        $this->measurement = $measurement;
        $this->value = $value;
    }
}