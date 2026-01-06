import re


class Item:
    def __init__(self, id, name, size, quantity, measurement, value):
        self.id = id
        self.name = name
        self.size = size
        self.quantity = quantity
        self.measurement = measurement
        self.value = value


class Consolare:
    def __init__(self, json_data):
        # Cabecalho
        self.cabecalho_1 = json_data.get('_CABECALHO_1')
        self.cabecalho_2 = json_data.get('_CABECALHO_2')
        self.cabecalho_3 = json_data.get('_CABECALHO_3')
        self.cabecalho_4 = json_data.get('_CABECALHO_4')
        self.cabecalho_NomeFuneraria = json_data.get('_CABECALHO_NomeFuneraria')
        self.cabecalho_Endereco = json_data.get('_CABECALHO_Endereco')
        self.cabecalho_Bairro = json_data.get('_CABECALHO_Bairro')
        self.cabecalho_CNPJ = json_data.get('_CABECALHO_CNPJ')
        
        # Dados OS
        self.dadosos_1 = json_data.get('_DADOSOS_1')
        self.dadosos_2 = json_data.get('_DADOSOS_2')
        self.dadosos_3 = json_data.get('_DADOSOS_3')
        self.dadosos_4 = json_data.get('_DADOSOS_4')
        self.dadosos_5 = json_data.get('_DADOSOS_5')
        self.dadosos_6 = json_data.get('_DADOSOS_6')
        self.dadosos_7 = json_data.get('_DADOSOS_7')
        self.dadosos_8 = json_data.get('_DADOSOS_8')
        self.dadosos_NumOrdem = json_data.get('_DADOSOS_NumOrdem')
        self.dadosos_Funeraria = json_data.get('_DADOSOS_Funeraria')
        self.dadosos_NotaFiscal = json_data.get('_DADOSOS_NotaFiscal')
        self.dadosos_DataInsert = json_data.get('_DADOSOS_DataInsert')
        self.dadosos_DataUpdate = json_data.get('_DADOSOS_DataUpdate')
        self.dadosos_TipoDeContratacao = json_data.get('_DADOSOS_TipoDeContratacao')
        self.dadosos_TipoDeContratacao_SPRegula = json_data.get('_DADOSOS_TipoDeContratacao_SPRegula')
        self.dadosos_DataEmissao = json_data.get('_DADOSOS_DataEmissao')
        self.dadosos_Agencia = json_data.get('_DADOSOS_Agencia')
        self.dadosos_Tel = json_data.get('_DADOSOS_Tel')
        self.dadosos_EnviadaParaPolo = json_data.get('_DADOSOS_EnviadaParaPolo')
        self.dadosos_EnviadaParaPolo_Nome = json_data.get('_DADOSOS_EnviadaParaPolo_Nome')
        
        # Contratante
        self.contratante_1 = json_data.get('_CONTRATANTE_1')
        self.contratante_2 = json_data.get('_CONTRATANTE_2')
        self.contratante_3 = json_data.get('_CONTRATANTE_3')
        self.contratante_4 = json_data.get('_CONTRATANTE_4')
        self.contratante_5 = json_data.get('_CONTRATANTE_5')
        self.contratante_6 = json_data.get('_CONTRATANTE_6')
        self.contratante_7 = json_data.get('_CONTRATANTE_7')
        self.contratante_8 = json_data.get('_CONTRATANTE_8')
        self.contratante_9 = json_data.get('_CONTRATANTE_9')
        self.contratante_Contratante = json_data.get('_CONTRATANTE_Contratante')
        self.contratante_GrauDeParentesco = json_data.get('_CONTRATANTE_GrauDeParentesco')
        self.contratante_NomeSocial = json_data.get('_CONTRATANTE_NomeSocial')
        self.contratante_CPF = json_data.get('_CONTRATANTE_CPF')
        self.contratante_RG = json_data.get('_CONTRATANTE_RG')
        self.contratante_Fone1 = json_data.get('_CONTRATANTE_Fone1')
        self.contratante_Fone2 = json_data.get('_CONTRATANTE_Fone2')
        self.contratante_Email = json_data.get('_CONTRATANTE_Email')
        self.contratante_Endereco = json_data.get('_CONTRATANTE_Endereco')
        self.contratante_EnderecoNumero = json_data.get('_CONTRATANTE_EnderecoNumero')
        self.contratante_EnderecoComplemento = json_data.get('_CONTRATANTE_EnderecoComplemento')
        self.contratante_Bairro = json_data.get('_CONTRATANTE_Bairro')
        self.contratante_Cidade = json_data.get('_CONTRATANTE_Cidade')
        self.contratante_UF = json_data.get('_CONTRATANTE_UF')
        self.contratante_Cep = json_data.get('_CONTRATANTE_Cep')
        
        # Falecido
        self.falecido_1 = json_data.get('_FALECIDO_1')
        self.falecido_2 = json_data.get('_FALECIDO_2')
        self.falecido_3 = json_data.get('_FALECIDO_3')
        self.falecido_4 = json_data.get('_FALECIDO_4')
        self.falecido_5 = json_data.get('_FALECIDO_5')
        self.falecido_6 = json_data.get('_FALECIDO_6')
        self.falecido_7 = json_data.get('_FALECIDO_7')
        self.falecido_8 = json_data.get('_FALECIDO_8')
        self.falecido_9 = json_data.get('_FALECIDO_9')
        self.falecido_10 = json_data.get('_FALECIDO_10')
        self.falecido_11 = json_data.get('_FALECIDO_11')
        self.falecido_Nome = json_data.get('_FALECIDO_Nome')
        self.falecido_Peso = json_data.get('_FALECIDO_Peso')
        self.falecido_Altura = json_data.get('_FALECIDO_Altura')
        self.falecido_NomeSocial = json_data.get('_FALECIDO_NomeSocial')
        self.falecido_Sexo = json_data.get('_FALECIDO_Sexo')
        self.falecido_Mae = json_data.get('_FALECIDO_Mae')
        self.falecido_DataNascimento = json_data.get('_FALECIDO_DataNascimento')
        self.falecido_DataFalecimento = json_data.get('_FALECIDO_DataFalecimento')
        self.falecido_HoraFalecimento = json_data.get('_FALECIDO_HoraFalecimento')
        self.falecido_RegistroPROAIM = json_data.get('_FALECIDO_RegistroPROAIM')
        self.falecido_CausaMorte1 = json_data.get('_FALECIDO_CausaMorte1')
        self.falecido_CausaMorte2 = json_data.get('_FALECIDO_CausaMorte2')
        self.falecido_CausaMorte3 = json_data.get('_FALECIDO_CausaMorte3')
        self.falecido_CausaMorte4 = json_data.get('_FALECIDO_CausaMorte4')
        
        # Remocao
        self.remocao_1 = json_data.get('_REMOCAO_1')
        self.remocao_2 = json_data.get('_REMOCAO_2')
        self.remocao_3 = json_data.get('_REMOCAO_3')
        self.remocao_4 = json_data.get('_REMOCAO_4')
        self.remocao_5 = json_data.get('_REMOCAO_5')
        self.remocao_6 = json_data.get('_REMOCAO_6')
        self.remocao_7 = json_data.get('_REMOCAO_7')
        self.remocao_8 = json_data.get('_REMOCAO_8')
        self.remocao_9 = json_data.get('_REMOCAO_9')
        self.remocao_RemocaoNome = json_data.get('_REMOCAO_RemocaoNome')
        self.remocao_RemocaoLogradouro = json_data.get('_REMOCAO_RemocaoLogradouro')
        self.remocao_RemocaoNumero = json_data.get('_REMOCAO_RemocaoNumero')
        self.remocao_RemocaoComplemento = json_data.get('_REMOCAO_RemocaoComplemento')
        self.remocao_RemocaoBairro = json_data.get('_REMOCAO_RemocaoBairro')
        self.remocao_RemocaoCidade = json_data.get('_REMOCAO_RemocaoCidade')
        self.remocao_RemocaoUF = json_data.get('_REMOCAO_RemocaoUF')
        self.remocao_RemocaoCEP = json_data.get('_REMOCAO_RemocaoCEP')
        self.remocao_RemocaoData = json_data.get('_REMOCAO_RemocaoData')
        self.remocao_RemocaoHora = json_data.get('_REMOCAO_RemocaoHora')
        self.remocao_LaboratorioNome = json_data.get('_REMOCAO_LaboratorioNome')
        self.remocao_LaboratorioLogradouro = json_data.get('_REMOCAO_LaboratorioLogradouro')
        self.remocao_LaboratorioNumero = json_data.get('_REMOCAO_LaboratorioNumero')
        self.remocao_LaboratorioComplemento = json_data.get('_REMOCAO_LaboratorioComplemento')
        self.remocao_LaboratorioBairro = json_data.get('_REMOCAO_LaboratorioBairro')
        self.remocao_LaboratorioCidade = json_data.get('_REMOCAO_LaboratorioCidade')
        self.remocao_LaboratorioUF = json_data.get('_REMOCAO_LaboratorioUF')
        self.remocao_LaboratorioCEP = json_data.get('_REMOCAO_LaboratorioCEP')
        self.remocao_VelorioNome = json_data.get('_REMOCAO_VelorioNome')
        self.remocao_VelorioLogradouro = json_data.get('_REMOCAO_VelorioLogradouro')
        self.remocao_VelorioNumero = json_data.get('_REMOCAO_VelorioNumero')
        self.remocao_VelorioComplemento = json_data.get('_REMOCAO_VelorioComplemento')
        self.remocao_VelorioBairro = json_data.get('_REMOCAO_VelorioBairro')
        self.remocao_VelorioCidade = json_data.get('_REMOCAO_VelorioCidade')
        self.remocao_VelorioUF = json_data.get('_REMOCAO_VelorioUF')
        self.remocao_VelorioCEP = json_data.get('_REMOCAO_VelorioCEP')
        self.remocao_VelorioData = json_data.get('_REMOCAO_VelorioData')
        self.remocao_VelorioHora = json_data.get('_REMOCAO_VelorioHora')
        self.remocao_VelorioDuracao = json_data.get('_REMOCAO_VelorioDuracao')
        
        # Sepultamento
        self.sepultamento_1 = json_data.get('_SEPULTAMENTO_1')
        self.sepultamento_2 = json_data.get('_SEPULTAMENTO_2')
        self.sepultamento_3 = json_data.get('_SEPULTAMENTO_3')
        self.sepultamento_4 = json_data.get('_SEPULTAMENTO_4')
        self.sepultamento_5 = json_data.get('_SEPULTAMENTO_5')
        self.sepultamento_SeCremacao = json_data.get('_SEPULTAMENTO_SeCremacao')
        self.sepultamento_Nome = json_data.get('_SEPULTAMENTO_Nome')
        self.sepultamento_Logradouro = json_data.get('_SEPULTAMENTO_Logradouro')
        self.sepultamento_Numero = json_data.get('_SEPULTAMENTO_Numero')
        self.sepultamento_Complemento = json_data.get('_SEPULTAMENTO_Complemento')
        self.sepultamento_Bairro = json_data.get('_SEPULTAMENTO_Bairro')
        self.sepultamento_Cidade = json_data.get('_SEPULTAMENTO_Cidade')
        self.sepultamento_UF = json_data.get('_SEPULTAMENTO_UF')
        self.sepultamento_Cep = json_data.get('_SEPULTAMENTO_Cep')
        self.sepultamento_Data = json_data.get('_SEPULTAMENTO_Data')
        self.sepultamento_Hora = json_data.get('_SEPULTAMENTO_Hora')
        
        # Itens
        self.itens_1 = json_data.get('_ITENS_1')
        self.itens_2 = self._parse_items(json_data.get('_ITENS_2', ''))
        self.itens_3 = json_data.get('_ITENS_3')
        self.itens_4 = json_data.get('_ITENS_4')
        self.itens_NumOrdem = json_data.get('_ITENS_NumOrdem')
        self.itens_Funeraria = json_data.get('_ITENS_Funeraria')
        self.itens_Observacoes = json_data.get('_ITENS_Observacoes')
        self.itens_ValorTotal = json_data.get('_ITENS_ValorTotal')
        
        # Pagamento
        self.pagamento_1 = json_data.get('_PAGAMENTO_1')
        self.pagamento_2 = json_data.get('_PAGAMENTO_2')
        self.pagamento_3 = json_data.get('_PAGAMENTO_3')
        self.pagamento_4 = json_data.get('_PAGAMENTO_4')
        self.pagamento_5 = json_data.get('_PAGAMENTO_5')
        self.pagamento_Situacao = json_data.get('_PAGAMENTO_Situacao')
        self.pagamento_TotalPago = json_data.get('_PAGAMENTO_TotalPago')
        
        # Versao
        self.versao_1 = json_data.get('_VERSAO_1')
        self.versao_Numero = json_data.get('_VERSAO_Numero')
        
        # Responsavel
        self.responsavel_1 = json_data.get('_RESPONSAVEL_1')
        self.responsavel_2 = json_data.get('_RESPONSAVEL_2')
        self.responsavel_3 = json_data.get('_RESPONSAVEL_3')
        self.responsavel_Nome = json_data.get('_RESPONSAVEL_Nome')
        self.responsavel_Documento = json_data.get('_RESPONSAVEL_Documento')
        
        # Atendente
        self.atendente_1 = json_data.get('_ATENDENTE_1')
        self.atendente_2 = json_data.get('_ATENDENTE_2')
        self.atendente_3 = json_data.get('_ATENDENTE_3')
        self.atendente_4 = json_data.get('_ATENDENTE_4')
        self.atendente_Codigo = json_data.get('_ATENDENTE_Codigo')
        self.atendente_Nome = json_data.get('_ATENDENTE_Nome')
        self.atendente_NomeContratante = json_data.get('_ATENDENTE_NomeContratante')
        
        # Nota Fiscal
        self.nota_Fiscal = json_data.get('NOTA_FISCAL')
        self.nota_Fiscal_URL = json_data.get('NOTA_FISCAL_URL')
        self.nota_Fiscal_Data = json_data.get('NOTA_FISCAL_DATA')

    def _parse_items(self, items_string):
        """Parse items from string format"""
        if not items_string:
            return []
        
        # Pattern: number#text#text#number#LETTERS#decimal
        pattern = r'(\d+)#([^#]*)#([^#]*)#(\d+)#([A-Z]+)#([\d.]+)(?:;|$)'
        matches = re.findall(pattern, items_string)
        
        items = []
        for match in matches:
            item_id, name, size, quantity, measurement, value = match
            items.append(Item(item_id, name, size, quantity, measurement, value))
        
        return items

    def make_address(self, cep, logradouro, numero, complemento, bairro):
        """Format address string"""
        cep = str(cep) if cep else ""
        formatted_cep = cep.zfill(8)
        formatted_cep = f"{formatted_cep[:5]}-{formatted_cep[5:]}"
        
        address = f"{str(logradouro).upper()} - Nº {str(numero).strip()}"
        if complemento:
            address += f" - {str(complemento).upper()}"
        address += f" - {str(bairro).upper()} - {formatted_cep}"
        
        return address

    def find_item_with_urna(self):
        """Find an item name that contains the substring 'URNA'"""
        for item in self.itens_2:
            if 'URNA' in item.name.upper():
                return item
        return None
