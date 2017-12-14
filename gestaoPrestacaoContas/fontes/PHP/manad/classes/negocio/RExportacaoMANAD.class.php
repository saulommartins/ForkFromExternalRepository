<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de Exportacao dos Arquivos do MANAD
    * Data de Criacao   : 07/11/2012
    *
    * @author Analista: Gelson Goncalves
    * @author Desenvolvedor: Matheus Figueredo
    *
    * @package URBEM
    * @subpackage Exportador
*/

/* Includes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                );

include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoOrgao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoUnidade.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoDespesa.class.php';
include_once CAM_GF_EXP_MAPEAMENTO.'FExportacaoTCERSExportacaoBalanceteReceita.class.php';
include_once CAM_GF_EXP_MAPEAMENTO.'FExportacaoTCERSExportacaoReceita.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeValorLancamento.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoConta.class.php';
include_once CAM_GF_EXP_MAPEAMENTO.'FExportacaoPagamento.class.php';
include_once CAM_GF_EXP_MAPEAMENTO.'FExportacaoLiquidacao.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php';
include_once CAM_GA_NORMAS_MAPEAMENTO.'TNorma.class.php';
include_once CAM_GPC_MANAD_MAPEAMENTO.'TExportacaoMANAD.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';
include_once(CAM_GPC_MANAD_MAPEAMENTO."TExportacaoMANADConfiguracao.class.php");
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoRestosPreEmpenho.class.php"   );
include_once(CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteReceita.class.php" );
include_once(CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteDespesa.class.php" );
include_once( CAM_GPC_TCERS_MAPEAMENTO."FExportacaoTCERSBalanceteDespesa.class.php");
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeBalanceteVerificacao.class.php"             );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php" 		);
/**
 * Classe de Regra para geracao de arquivos do MANAD.
 *
 * @author   Desenvolvedor :   Gelson Goncalves
 * @author   Analista      :   Matheus Figueredo
*/
class RExportacaoMANAD
{
    /* Valores entre*/
    public $stCodEntidades;
    public $arCodEntidades = array();
    public $stExercicio   ;
    public $inCodOrgao    ;
    public $stDataInicial ;
    public $stDataFinal   ;
    public $arArquivos = array();
    public $obFExportacaoTCERSExportacaoBalanceteReceita  ;
    public $obFExportacaoTCERSExportacaoReceita           ;
    public $obTContabilidadeValorLancamento     ;
    public $obTContabilidadePlanoConta          ;
    public $obTNorma                            ;
    public $obFExportacaoBalanceteDespesa       ;
    public $arCountRegistros = array()          ;

    /**
    * Metodo Construtor
    * @access Private
    */
    public function RExportacaoMANAD()
    {
        $this->obFExportacaoTCERSExportacaoBalanceteReceita   =   new FExportacaoTCERSExportacaoBalanceteReceita() ;
        $this->obFExportacaoTCERSExportacaoReceita           =   new  FExportacaoTCERSExportacaoReceita() ;
        $this->obTContabilidadeValorLancamento      =   new TContabilidadeValorLancamento() ;
        $this->obTContabilidadePlanoConta           =   new TContabilidadePlanoConta();
        $this->obTNorma                             =   new TNorma();
        $this->obFExportacaoBalanceteDespesa        =   new FOrcamentoBalanceteDespesa();
        $this->obTExportacaoMANAD                   =   new TExportacaoMANAD();
    }

    /**
     * Setters
     */
    public function setCodEntidades($valor)
    {
        $this->stCodEntidades = $valor;
    }

    public function setCodEntidadesArray($valor)
    {
        $this->arCodEntidades = $valor;
    }

    public function setExercicio($valor)
    {
       $this->stExercicio = $valor;
    }

    public function setCodOrgao($valor)
    {
       $this->inCodOrgao = $valor;
    }

    public function setArquivos($valor)
    {
       $this->arArquivos = $valor;
    }

    public function setDataInicial($valor)
    {
       $this->stDataInicial = $valor;
    }

    public function setDataFinal($valor)
    {
       $this->stDataFinal = $valor;
    }

    /**
     * Getters
     */
    public function getCodEntidades()
    {
        return $this->stCodEntidades;
    }

    public function getCodEntidadesArray()
    {
        return $this->arCodEntidades;
    }

    public function getExercicio()
    {
        return $this->stExercicio;
    }

    public function getCodOrgao()
    {
       return $this->inCodOrgao;
    }

    public function getArquivos()
    {
       return $this->arArquivos;
    }

    public function getDataInicial()
    {
        return $this->stDataInicial;
    }

    public function getDataFinal()
    {
       return $this->stDataFinal;
    }

    /**
     * Gera lista com opcoes de Finalidade, conforme item 4.1, tabela 5
     *
     * @return RecordSet
     */
    public function geraListaFinalidades(&$rsListaFinalidades)
    {
        $arFinalidades[0]["cod_fin"] = "61";
        $arFinalidades[0]["nom_fin"] = "Solicita��o de Auditor-Fiscal";
        $arFinalidades[1]["cod_fin"] = "62";
        $arFinalidades[1]["nom_fin"] = "Entrega na Secretaria da Receita Previdenciaria";
        $arFinalidades[2]["cod_fin"] = "90";
        $arFinalidades[2]["nom_fin"] = "Dados Internos UF";

        $rsListaFinalidades = new RecordSet();
        $rsListaFinalidades->preenche($arFinalidades);
    }

    /**
     * Totaliza o numero de linhas do tipo passado por parametro
     *
     * @return integer
     */
    public function getNumLinhas($stTipoRegistro)
    {
        $inCount = 0;

        if ($stTipoRegistro == "0") {
            $inCount =  $this->arCountRegistros['0000'] +
                        $this->arCountRegistros['0001'] +
                        $this->arCountRegistros['0050'] +
                        $this->arCountRegistros['0100'] +
                        $this->arCountRegistros['0990'];
//        } elseif ($stTipoRegistro == "I") {
//            $inCount =  $this->arCountRegistros['I001'] +
//                        $this->arCountRegistros['I005'] +
//                        $this->arCountRegistros['I050'] +
//                        $this->arCountRegistros['I100'] +
//                        $this->arCountRegistros['I150'] +
//                        $this->arCountRegistros['I200'] +
//                        $this->arCountRegistros['I250'] +
//                        $this->arCountRegistros['I990'];
        } elseif ($stTipoRegistro == "K") {
            $inCount =  $this->arCountRegistros['K001'] +
                        $this->arCountRegistros['K050'] +
                        $this->arCountRegistros['K100'] +
                        $this->arCountRegistros['K150'] +
                        $this->arCountRegistros['K200'] +
                        $this->arCountRegistros['K250'] +
                        $this->arCountRegistros['K300'] +
                        $this->arCountRegistros['K990'];
        } elseif ($stTipoRegistro == "L") {
            $inCount =  $this->arCountRegistros['L001'] +
                        $this->arCountRegistros['L050'] +
                        $this->arCountRegistros['L100'] +
                        $this->arCountRegistros['L150'] +
                        $this->arCountRegistros['L200'] +
                        $this->arCountRegistros['L250'] +
                        $this->arCountRegistros['L300'] +
                        $this->arCountRegistros['L350'] +
                        $this->arCountRegistros['L400'] +
                        $this->arCountRegistros['L450'] +
                        $this->arCountRegistros['L500'] +
                        $this->arCountRegistros['L550'] +
                        $this->arCountRegistros['L600'] +
                        $this->arCountRegistros['L650'] +
                        $this->arCountRegistros['L700'] +
                        $this->arCountRegistros['L750'] +
                        $this->arCountRegistros['L800'] +
                        $this->arCountRegistros['L990'];
        } elseif ($stTipoRegistro == "9") {
            $inCount =  $this->arCountRegistros['9001'] +
                        $this->arCountRegistros['9900'] +
                        $this->arCountRegistros['9990'] +
                        $this->arCountRegistros['9999'];
        }

        return $inCount;
    }

    /**
     * Monta um recordset com o tipo registro e o numero de linhas correspondente
     *
     * @return RecordSet
     */
    public function getRecordSetLinhas()
    {
        $rsLinhas = new RecordSet;
        $inCount = 0;
        $arTMP = array();

        foreach ($this->arCountRegistros AS $stTipo => $inCountLinhas) {
            $arTMP[$inCount] = array (
                'reg'     => '9900',
                'tip_reg' => $stTipo,
                'qtd_reg' => $inCountLinhas
            );
            $inCount++;
        }

        $rsLinhas->preenche($arTMP);

        return $rsLinhas;
    }

    /**
     * Bloco obrigatorio em todos os arquivos digitais
     *
     * Bloco 0 (zero) - ABERTURA, IDENTIFICACAO E REFERENCIAS
     */
    public function geraBloco0(&$obExportador)
    {
        #######################################################################################################
        # Registro tipo 0000
        # Abertura do arquivo digital e identificacao dos estabelecimentos (inclusive obras) do contribuinte.
        #
        # Observacoes:
        #   Registros: obrigatorios (ao menos um registro do tipo 0000)
        #   Ocorrencia: Varios (1 por estabelecimento ou obra de construcao civil da empresa, dentro do periodo fiscalizado)
        #######################################################################################################
        $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio());
        $this->obTExportacaoMANAD->setDado('dtInicial'     , $this->getDataInicial());
        $this->obTExportacaoMANAD->setDado('dtFinal'       , $this->getDataFinal());
        $this->obTExportacaoMANAD->setDado('stCodEntidades', $this->getCodEntidades());
        $this->obTExportacaoMANAD->recuperaDadosData($rsRegistroData);
        $arrRegistroData[] = $rsRegistroData->getElementos();
        $stDtInicial = '0101'.$rsRegistroData->getCampo('exercicio');

        //Dados
        $obOrcamentoEntidade = new TOrcamentoEntidade();
        $obOrcamentoEntidade->setDado('exercicio', $this->getExercicio());
        $obOrcamentoEntidade->setDado('stDtInicial', $stDtInicial);
        $obOrcamentoEntidade->setDado('stDtFinal', str_replace("/", "", $this->getDataFinal()));
        $obOrcamentoEntidade->recuperaDadosMANAD($rsRegistro0000, $this->getCodEntidades());

        $this->arCountRegistros['0000'] = $rsRegistro0000->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro0000);

        //01 - REG - Texto Fixo Contendo '0000' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - NOME - Nome empresarial do contribuinte (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //03 - CNPJ - Numero de inscricao do contribuinte no CNPJ (Numerico de tamanho 14).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        //04 - CPF - Numero de inscricao do contribuinte no CPF (Numerico de tamanho 11).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        //05 - CEI - Numero de inscricao no Cadastro Especifico do INSS (Numerico de tamanho 12).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cei");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        //06 - NIT - Numero de inscricao no cadastro correspondente (PIS/PASEP/CI/SUS) (Numerico de tamanho 11).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nit");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        //07 - UF - Sigla da unidade da federacao do contribuinte (Caracter de tamanho 2).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        //08 - IE - Numero de Inscricao Estadual do contribuinte (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ie");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //09 COD_MUN - Codigo do municipio do domicilio fiscal do contribuinte, cofnrme a tabela do IBGE (Numerico de tamanho 7).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_mun");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(07);

        //10 - IM - Numero de Inscricao Municipal do contribuinte (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("im");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //11 - SUFRAMA - Numero de Inscricao do contribuinte na SUFRAMA (Caracter de tamanho 9).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("suframa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(09);

        //12 - IND_CENTR - Indicador de centralizacao de escrituracao: (Numerico de tamanho 1)
        //                     0 - Estabelecimento sem centralizacao de escrituracao;
        //                     1 - Estabelecimento centralizador de escrituracao;
        //                     2 - Estabelecimento com escrituracao centralizada;
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_centr");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        //13 - DT_INI - Data inicial das informacoes contidas no arquivo (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_ini");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //14 - DT_FIN - Data final das informacoes contidas no arquivo (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_fin");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //15 - COD_VER - Codigo da versao do leiaute conforme item 4.1, tabela 4 (Numerico de tamanho 3).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ver");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        //16 - COD_FIN - Codigo da finalidade do arquivo conforme item 4.1, tabela 5 (Numerico de tamanho 2).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fin");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        //17 - IND_ED - Indicador de tipo de entrada de dados: (Numerico de tamanho 1).
        //                 0 - Digitacao de dados;
        //                 1 - Importacao de arquivo texto (arquivo construido a partir da manipulacao de dados extaidos do sistema)
        //                 2 - Validacao de arquivo texto (arquivo gerado diretamente por sistema informatizado, ja no leiaute requerido)
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_ed");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        #######################################################################################################
        # Registro tipo 0001
        # Abertura do bloco 0 (zero)
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #######################################################################################################

        $arRegistro0001 = array( 0 => array (
            'reg' => '0001',
            'ind_mov' => 0
        ));

        $rsRegistro0001 = new RecordSet;
        $rsRegistro0001->preenche($arRegistro0001);

        $this->arCountRegistros['0001'] = $rsRegistro0001->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro0001);

        //01 - REG - Texto Fixo Contendo '0001' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - IND_MOV - Indicador de movimento: (Numerico de tamanho 1)
        //                  0 - Bloco com dados informados;
        //                  1 - Bloco sem dados informados;
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_mov");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        #######################################################################################################
        # Registro tipo 0050
        # Dados do contabilista
        #
        # Observacoes:
        #   Ocorrencia: Varios (todos os contabilistas que prestaram servico a� empresa dentro do periodo das informacoes)
        #######################################################################################################

        $this->obTExportacaoMANAD->setDado('exercicio', $this->getExercicio());
        $this->obTExportacaoMANAD->setDado('stDtInicial', str_replace("/", "", $this->getDataInicial()));
        $this->obTExportacaoMANAD->setDado('stDtFinal', str_replace("/", "", $this->getDataFinal()));
        $this->obTExportacaoMANAD->recuperaDados0050($rsRegistro0050);

        $this->arCountRegistros['0050'] = $rsRegistro0050->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro0050);

        //01 - REG - Texto Fixo Contendo '0050' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - NOME - Nome do contabilista/escritorio de contabilidade (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //03 - CNPJ - Numero de Inscricao do escritorio de contabilidade no CNPJ (Numerico de tamanho 14).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        //04 - CPF - Numero de Inscricao do contabilista no CPF (Numerico de tamanho 11).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        //05 - CRC - Numero de Inscricao do contabilista no Conselho Regional de Contabilidade (Caracter de tamanho 11).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("crc");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        //06 - DT_INI - Data de inicio da prestacao do servico de contabilidade (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_ini");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //07 - DT_FIN - Data de termino da prestacao do servico de contabilidade (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_fin");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //08 - END - Endereco do imovel (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("end");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //09 - NUM - Numero do imovel (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //10 - COMPL - Dados complementares do endereco (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("compl");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //11 - BAIRRO - Bairro em que o imovel esta situado (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //12 - CEP - Codigo de Enderecamento Postal (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        //13 - UF - Sigla da unidade da federacao do contabilista/escritorio de contabilidade (Caracter de tamanho 2).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        //14 - CP - Caixa Postal (Numero de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        //15 - CEP_CP - Codigo de Enderecamento Postal da Caixa Postal (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep_cp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //16 - FONE - Numero do telefone (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //17 - FAX - Numero do fax (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fax");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //18 - EMAIL - Endereco do correio eletronico (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        #######################################################################################################
        # Registro tipo 0100
        # Dados do tecnico/empresa responsavel pela geracao do arquivo digital
        #
        # Observacoes:
        #   Ocorrencia: Varios (todos os tecnicos/empresas que prestaram servico a� empresa dentro do periodo das informacoes)
        #######################################################################################################

        $this->obTExportacaoMANAD->setDado('username', Sessao::getUsername());
        $this->obTExportacaoMANAD->recuperaDados0100($rs0100);

        $arRegistro0100 = array( 0 => array(
            'reg' => '0100',
            'emp_tec' => 'Confedera��o Nacional de Municipios',
            'dt_ini_serv_inf' => str_replace("/", "", $this->getDataInicial()),
            'dt_fin_serv_inf' => str_replace("/", "", $this->getDataFinal()),
            'cnpj' => '00703157000183',
            'fone' => '(61) 21016000',
            'fax' => '(61) 21016008',
            'email' => 'suporte@cnm.org.br',
            'cpf' => $rs0100->getCampo('cpf')
        ));

        $rsRegistro0100 = new RecordSet;
        $rsRegistro0100->preenche($arRegistro0100);

        $this->arCountRegistros['0100'] = $rsRegistro0100->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro0100);

        //01 - REG - Texto Fixo Contendo '0100' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - EMP_TEC - Nome da empresa/tecnico pela geracao do arquivo (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("emp_tec");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //03 - CARGO - Cargo do responsavel na empresa (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cargo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //04 - DT_INI_SERV_INF - Data de inicio da prestacao do servico de informatica (fornecimento do sistema de geracao dos arquivos digitais) (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_ini_serv_inf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //05 - DT_FIN_SERV_INF - Data de termino da prestacao do servico de informatica (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_fin_serv_inf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //06 - CNPJ - Numero de Inscricao da empresa responsavel pela geracao do arquivo no CNPJ (Numerico de tamanho 14).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        //07 - CPF - Numero de Inscricao do tecnico responsavel pela geracao do arquivo no CPF (Numerico de tamanho 11).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        //08 - FONE - Numero do telefone (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //09 - FAX - Numero do fax (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fax");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //18 - EMAIL - Endereco do correio eletronico (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        #######################################################################################################
        # Registro tipo 0990
        # Encerramento do bloco 0
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #   A quantidade total de linhas do bloco 0 devera levar em consideracao todos os registros entre o primeiro 0000 e o 0990.
        #######################################################################################################

        $arRegistro0990 = array( 0 => array(
            'reg' => '0990',
            'qtd_lin_0' => $this->getNumLinhas('0')+1,
        ));

        $rsRegistro0990 = new RecordSet;
        $rsRegistro0990->preenche($arRegistro0990);
        $this->arCountRegistros['0990'] = $rsRegistro0990->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro0990);

        //01 - REG - Texto Fixo Contendo '0990' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - QTD_LIN_0 - Quantidade total de linhas do Bloco 0 (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_lin_0");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    }

//    /**
//     * Bloco obrigatorio as empresas definidas no item 1.2 - Regime Juridico de Direito Privado
//     *
//     * Bloco I - Lancamentos Contabeis
//     */
//    public function geraBlocoI(&$obExportador) {
//
//        // #######################################################################################################
//        // # Registro tipo I001
//        // # Abertura do bloco I
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: 1 por arquivo
//        // #######################################################################################################
//
//         $arRegistroI001 = array( 0 => array(
//             'reg' => 'I001',
//             'ind_mov' => 0,
//         ));
//
//         $rsRegistroI001 = new RecordSet;
//         $rsRegistroI001->preenche($arRegistroI001);
//
//         $this->arCountRegistros['I001'] = $rsRegistroI001->getNumLinhas();
//
//         $obExportador->roUltimoArquivo->addBloco($rsRegistroI001);
//
//         //01 - REG - Texto Fixo Contendo 'I001' (Caracter de tamanho 4).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - IND_MOV - Indicador de movimento: (Numerico de tamanho 1)
//         //                  0 - Bloco com dados informados;
//         //                  1 - Bloco sem dados informados;
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_mov");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
//
//        // #######################################################################################################
//        // # Registro tipo I005
//        // # Identificacao da escrituracao contabil
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: 1 por tipo de escrituracao
//        // #               Devem ser identificadas, ao longo dos registros I005, todas as contas analiticas da escrituracao contabil
//        // #               que sofram escrituracao globalizada ou por totais, com a utilizacao de registros auxiliares. Relativamente
//        // #               a estas contas, a empresa estara obrigada a� apresentacao dos registros auxiliares, em arquivo digital
//        // #               distinto, quando solicitado pela autoridade requisitante.
//        // #######################################################################################################
//
//        // //SQL para pesquisa
//
////         $this->obTExportacaoMANAD->setDado('stExercicio'    , $this->getExercicio()        );
////         $this->obTExportacaoMANAD->setDado('dtInicial'      , $this->getDataInicial()      );
////         $this->obTExportacaoMANAD->setDado('dtFinal'        , $this->getDataFinal()        );
////         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );
////         $obErro =    $this->obTExportacaoMANAD->recuperaDadosI005($rsRegistroI005);
//          $arRegistroI005 = array( 0 => array(
//             'reg' => 'I005',
//             'ind_esc' => 'G',
//         ));
//         $rsRegistroI005 = new RecordSet;
//         $rsRegistroI005->preenche($arRegistroI005);
//
//         $this->arCountRegistros['I005'] = $rsRegistroI005->getNumLinhas();
//         $obExportador->roUltimoArquivo->addBloco($rsRegistroI005);
//
//         //01 - REG - Texto Fixo Contendo 'I005' (Caracter de tamanho 4).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - IND_ESC - Indicador do tipo de escrituracao contabil:
//         //                  G - Diario Geral;
//         //                  A - Diario Auxiliar/Razao Auxiliar (inclusive Livro Caixa)
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_esc");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
//
//         //03 - COD_CTA - Codigo da conta analitica do Diario Geral que recebe os lancamentos globais (se IND_ESC = A)
//         //$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta");
//         //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
//
//        // #######################################################################################################
//        // # Registro tipo I050
//        // # Plano de contas
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: Varios
//        // #               Para as contas sinteticas de nivel mais elevado, o campo COD_GRP_CTA_SUP deve ficar vazio.
//        // #               O codigo da conta informado deve permitir a identificacao da mesma de forma unica. Assim sendo, no caso de
//        // #               empresa que trabalhe com classificacao identica para diversas contas analiticas, devera ser informado o
//        // #               codigo reduzido ou outro que identifique a conta de maneira unica. A codificacao utilizada devera ser
//        // #               observada tambem nos demais registros do bloco I onde constar campo especifico para codigo da conta, bem
//        // #               como no registro K200 (contabilizacao da folha de pagamento)
//        // #######################################################################################################
//
//         //SQL para pesquisa
//         $stFiltro="";
//          if ( $this->getCodEntidades() != "" ) {
//
//            $stFiltro = "\n cod_entidade IN  (".$this->getCodEntidades(). ") ";
//         }
//         $mesAno =substr(str_replace("/", "", $this->getDataFinal()), 2);
//
//
//         $this->obTExportacaoMANAD->setDado('stExercicio'   , $this->getExercicio());
//         $this->obTExportacaoMANAD->setDado('dtInicial'     , $this->getDataInicial());
//         $this->obTExportacaoMANAD->setDado('dtFinal'       , $this->getDataFinal());
//         $this->obTExportacaoMANAD->setDado('stFiltro'       ,$stFiltro);
//         $this->obTExportacaoMANAD->setDado('stExercicioLogado'    , Sessao::getExercicio()  );
//         $obErro =    $this->obTExportacaoMANAD->recuperaDadosI050($rsRegistroI050);
//
//         $this->arCountRegistros['I050'] = $rsRegistroI050->getNumLinhas();
//         $obExportador->roUltimoArquivo->addBloco($rsRegistroI050);
//
//         //01 - REG - Texto Fixo Contendo 'I050' (Caracter de tamanho 4).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - DT_INC_ALT - Data da inclusao ou alteracao da conta no sistema contabil da empresa, conforme item 3.1.10
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inc_alt");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
//
//         //03 - IND_NAT - Indicador da natureza:
//         //          Contas Patrimoniais:
//         //              1 - Ativo
//         //              2 - Passivo
//         //              3 - Patrimonio Liquido
//         //          Contas de Resultado:
//         //              4 - Despesa ou Custo
//         //              5 - Receita
//         //              9 - Outros
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_nat");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
//
//         //04 - IND_GRP_CTA - Indicador do tipo de grupo:
//         //                  S - Sintetico
//         //                  A - Analitico
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_grp_cta");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
//
//         //05 - NiVEL - Nivel da conta/grupo de contas
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nivel");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//
//         //06 - COD_GRP_CTA - Codigo da conta/grupo de contas
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_grp_cta");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//        //07 - COD_GRP_CTA_SUP - Codigo da conta/grupo de contas de nivel imediatamente superior
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_grp_cta_sup");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //08 - NOME_GRP_CTA - Nome da conta/grupo de contas
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_grp_cta");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//        // #######################################################################################################
//        // # Registro tipo I100
//        // # Centro de custos
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: varios
//        // #######################################################################################################
//
//        //SQL para pesquisa
//        $this->obTExportacaoMANAD->setDado('stExercicio'   , $this->getExercicio());
//        $this->obTExportacaoMANAD->setDado('dtInicial'     , $this->getDataFinal());
//        $this->obTExportacaoMANAD->setDado('dtFinal'       , $this->getDataFinal());
//        $this->obTExportacaoMANAD->setDado('stCodEntidades', $this->getCodEntidades());
//        $obErro = $this->obTExportacaoMANAD->recuperaDadosI100($rsRegistroI100);
//
//         $this->arCountRegistros['I100'] = $rsRegistroI100->getNumLinhas();
//         $obExportador->roUltimoArquivo->addBloco($rsRegistroI100);
//
//         //01 - REG - Texto fixo contendo "I100"
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - DT_INC_ALT - Data da inclusao ou alteracao do centro de custo, se houver, conforme item 3.1.10
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inc_alt");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
//
//         //03 - COD_CCUS - Codigo do centro de custos
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ccus");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //04 - CCUS - Nome do centro de custos
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ccus");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//        // #######################################################################################################
//        // # Registro tipo I150
//        // # Saldos mensais
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: varios
//        // #               Devem ser informados os saldos mensais de todas as contas analiticas, inclusive daquelas que nao possuiram
//        // #               movimentacao no mes.
//        // #######################################################################################################
//
//         //SQL para pesquisa
//         $stFiltro = "";
//
//         //seta elementos do filtro para ENTIDADE
//         if ( $this->getCodEntidades() != "" ) {
//
//            $stFiltro = "\n cod_entidade IN  (".$this->getCodEntidades(). ") ";
//         }
//         $mesAno =substr(str_replace("/", "", $this->getDataFinal()), 2);
//
//
//         $this->obTExportacaoMANAD->setDado('stExercicio'   , $this->getExercicio());
//         $this->obTExportacaoMANAD->setDado('dtInicial'     , $this->getDataInicial());
//         $this->obTExportacaoMANAD->setDado('dtFinal'       , $this->getDataFinal());
//         $this->obTExportacaoMANAD->setDado('mesAno'       , $mesAno);
//         $this->obTExportacaoMANAD->setDado('stFiltro'       ,$stFiltro);
//         $this->obTExportacaoMANAD->recuperaDadosI150($rsRegistroI150);
//
//         $this->arCountRegistros['I150'] = $rsRegistroI150->getNumLinhas();
//         $obExportador->roUltimoArquivo->addBloco($rsRegistroI150);
//
//         //01 - REG - Texto Fixo Contendo 'I150' (Caracter de tamanho 4).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - COD_CTA - Codigo da conta analitica
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //03 - COMP_SALDO - Mes e ano a que se referem os saldos inicial e final, conforme item 3.1.6
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("comp_saldo");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_MMYYYY");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
//
//         //04 - VL_SLD_INI - Valor do saldo inicial. No caso de saldo inicial nulo, preencher com 0 (zero).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_sld_ini");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
//         //05 - IND_SLD_INI - Indicador da situacao do saldo inicial:
//         //                  D- Devedor;
//         //                  C- Credor
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_sld_ini");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
//
//         //06 - VL_DEB - Valor total dos debitos no mes. No caso de nao ter havido debitos, preencher com 0 (zero).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_deb");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
//
//         //07 - VL_CRED - Valor total dos creditos no mes. No caso de nao ter havido creditos, preencher com 0 (zero)
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cred");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
//
//         //08 - VL_SLD_FIN - Valor do saldo final. No caso de saldo final igual nulo, preencher com 0 (zero).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_sld_fin");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
//
//         //09 - IND_SLD_FIN - Indicador da situacao do saldo final:
//         //                  D- Devedor;
//         //                  C- Credor
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_sld_fin");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//        // #######################################################################################################
//        // # Registro tipo I200
//        // # Lançamento contábil
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: varios
//        // #               O registro no arquivo de lancamentos contabeis sera considerado como partidas simples. Desta forma, tem-se:
//        // #                   - no caso de um lancamento com um debito e um credito, utiliza-se um registro que represente o debito e
//        // #                   um registro que represente o credito. O campo "COD_CP" deve ser preenchido em ambos os registros;
//        // #                   - no caso de um lancamento com um debito e diversos créditos, utiliza-se um registro que represente o
//        // #                   debito e tantos registros quantos sejam necessarios para representar os creditos, deixando em branco o
//        // #                   campo "COD_CP" no registro correspondente ao debito;
//        // #                   - no caso de um lancamento com um credito e diversos debitos, utiliza-se um registro que represente o
//        // #                   credito e tantos registros quantos sejam necessarios para representar os debitos, deixando em branco o
//        // #                   campo "COD_CP" no registro correspondente ao credito.
//        // #                   - no caso de um lancamento com diversos debitos e creditos concomitantes, utilizam-se tantos registros
//        // #                   quanto sejam necessarios para representar os debitos e creditos, deixando em branco o campo “COD_CP” nos
//        // #                   registros correspondentes aos debitos e creditos.
//        // #######################################################################################################
//
//        // //SQL para pesquisa
//        $arFiltro = Sessao::read('filtroRelatorio');
//
//
//        $this->obTExportacaoMANAD->setDado('stExercicio'   , $this->getExercicio());
//        $this->obTExportacaoMANAD->setDado('dtInicial'     , $this->getDataInicial());
//        $this->obTExportacaoMANAD->setDado('dtFinal'       , $this->getDataFinal());
//        $this->obTExportacaoMANAD->setDado('stEntidade'       ,$this->getCodEntidades() );
//        $this->obTExportacaoMANAD->recuperaDadosI200($rsRegistroI200);
//
//        $this->arCountRegistros['I200'] = $rsRegistroI200->getNumLinhas();
//         $obExportador->roUltimoArquivo->addBloco($rsRegistroI200);
//
//         //01 - REG - Texto Fixo Contendo 'I200' (Caracter de tamanho 4).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - DT_LCTO - Data do lancamento
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lcto");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
//
//         //03 - COD_CTA - Codigo da conta analitica debitada/creditada
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //04 - COD_CCUS - Coodigo do centro de custos (Caracter de tamanho indefinido).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ccus");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //05 - COD_CP - Código da conta de contrapartida analitica debitada/creditada. (Caracter de tamanho indefinido).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cp");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //06 - VL_DEB_CRED - Valor total debitado/creditado.
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_deb_cred");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
//
//         //07 - IND_DEB_CRED - Indicador da natureza do lancamento:
//         //                  D- Debito;
//         //                  C- Credito
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_deb_cred");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
//
//         //08 - NUM_ARQ - Numero ou codigo de arquivamento dos documentos
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_arq");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //09 - NUM_LCTO - Numero ou codigo do lancamento contabil
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lcto");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //10 - IND_LCTO - Indicador do tipo de lancamento:
//         //              N- Lancamento normal;
//         //              E- Lancamento de encerramento
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_lcto");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
//
//         //11 - HIST_LCTO - Historico do lancamento
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hist_lcto");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//        // #######################################################################################################
//        // # Registro tipo I250
//        // # Saldos das contas de resultado antes do encerramento
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: varios
//        // #######################################################################################################
//
//         //SQL para pesquisa
//
//        $stFiltro .= "AND l.cod_entidade IN  (".$this->getCodEntidades(). ") AND ";
//        $stFiltro .= " l.exercicio = '". Sessao::getExercicio()."' AND ";
//        $stFiltro .= " lo.dt_lote >= TO_DATE('".$this->getDataInicial()."','dd/mm/yyyy' ) AND ";
//        $stFiltro .= " lo.dt_lote <= TO_DATE('".$this->getDataFinal()."','dd/mm/yyyy') ";
//
//        $this->obTExportacaoMANAD->setDado('stExercicio'   , $this->getExercicio());
//        $this->obTExportacaoMANAD->setDado('stFiltro'       ,$stFiltro);
//        $this->obTExportacaoMANAD->setDado('stEntidade'       ,$this->getCodEntidades() );
//        $this->obTExportacaoMANAD->recuperaDadosI250($rsRegistroI250);
//
//        $this->arCountRegistros['I250'] = $rsRegistroI250->getNumLinhas();
//
//        $obExportador->roUltimoArquivo->addBloco($rsRegistroI250);
//
//         //01 - REG - Texto Fixo Contendo 'I250' (Caracter de tamanho 4).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - DT_RES - Data da apuracao do resultado
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_res");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
//
//         //03 - COD_GRP_CTA - Codigo da conta analitica de resultado
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_grp_cta");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//
//         //04 - VL_GRP_CTA - Saldo final antes do lançamento de encerramento
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_grp_cta");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
//
//         //05 - SLD_FIN - Indicador da situacao do saldo final:
//         //              D- Devedor;
//         //              C- Credor
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sld_fin");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
//
//        // #######################################################################################################
//        // # Registro tipo I990
//        // # Encerramento do bloco I
//        // #
//        // # Observacoes:
//        // #   Ocorrencia: 1 por arquivo
//        // #   A quantidade total de linhas no bloco I devera levar em consideracao todos os registros entre o I001 e o I990, inclusive.
//        // #######################################################################################################
//
//         $arRegistroI990 = array( 0 => array(
//             'reg' => 'I990',
//             'qtd_lin_i' => $this->getNumLinhas('I')+1,
//         ));
//
//         $rsRegistroI990 = new RecordSet;
//         $rsRegistroI990->preenche($arRegistroI990);
//         $this->arCountRegistros['I990'] = $rsRegistroI990->getNumLinhas();
//
//         $obExportador->roUltimoArquivo->addBloco($rsRegistroI990);
//         $obExportador->roUltimoArquivo->setTipoDocumento('manad');
//
//         //01 - REG - Texto Fixo Contendo 'I990' (Caracter de tamanho 4).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
//         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);
//
//         //02 - QTD_LIN_I - Quantidade total de linhas do Bloco I (Numerico de tamanho indefinido).
//         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_lin_i");
//         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
//    }

    /**
     * Bloco obrigatorio as empresas definidas no item 1.2 - Direito Privado e 1.7 - Direito Publico
     *
     * Bloco K - Folha Pagamento
     *
     * Aplica-se a:
     *  a) empregados em geral;
     *  b) contribuintes individuais;
     *  c) servidores publicos estaveis e nao estaveis;
     *  d) contratos temporaios;
     *  e) agentes politicos;
     *  f) estagiarios;
     *
     * No bloco K, alem de outras informacoes, estarao fornecidos os dados e as codificacoes proprias utilizadas pelo contribuinte.
     * As codificacoes sao:
     *
     *  a) codificacoes de responsabilidade do contribuinte:
     *      - lotacao
     *      - itens da folha
     *      - cadastro de trabalhadores
     *  b) codificacoes normatizadas:
     *      - categoria de trabalahdores usadas na GFIP
     *      - Codigo Brasileiro de Ocupacao
     *
     */
    public function geraBlocoK(&$obExportador)
    {
        include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';
        include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';

        $arDataInicial = explode('/',$this->getDataInicial());
        $inMesInicial = $arDataInicial[1];
        $inAnoInicial= $arDataInicial[2];

        $arDataFinal = explode('/',$this->getDataFinal());
        $inMesFinal= $arDataFinal[1];
        $inAnoFinal= $arDataFinal[2];

        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mesInicial", $inMesInicial);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("anoInicial", $inAnoInicial);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mesFinal", $inMesFinal);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("anoFinal", $inAnoFinal);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaIntervaloPeriodosMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

        $codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, $this->getExercicio());

        foreach ($this->getCodEntidadesArray() as $inCodEntidade) {
            $obTEntidade = new TEntidade();
            $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
            $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);

            if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura ==$inCodEntidade ) {
                $arEsquemasEntidades[] = $inCodEntidade;
            }
        }

        #######################################################################################################
        # Registro tipo K001
        # Abertura do bloco K
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #######################################################################################################

        $arRegistroK001 = array( 0 => array (
            'reg' => 'K001',
            'ind_mov' => 0
        ));

        $rsRegistroK001 = new RecordSet;
        $rsRegistroK001->preenche($arRegistroK001);

        $this->arCountRegistros['K001'] = $rsRegistroK001->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistroK001);

        //01 - REG - Texto Fixo Contendo 'K001' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - IND_MOV - Indicador de movimento: (Numerico de tamanho 1)
        //                  0 - Bloco com dados informados;
        //                  1 - Bloco sem dados informados;
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_mov");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        #######################################################################################################
        # Registro tipo K050
        # Cadastro de trabalhadores
        #
        # Observacoes:
        #   Ocorrencia: Varios
        #######################################################################################################

        $this->arCountRegistros['K050'] = 0;
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            foreach ($arEsquemasEntidades as $codEntidade) {

                if ($codEntidadePrefeitura !=$codEntidade) {
                    $stEntidade = '_'.$codEntidade;
                } else {
                    $stEntidade = '';
                }

                $stArquivoK050 = "rsArquivoK050";
                $stArquivoK050 .= $stEntidade;
                $stArquivoK050 .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
                $$stArquivoK050 = new RecordSet();

                $this->obTExportacaoMANAD->setDado('entidade', $stEntidade);
                $this->obTExportacaoMANAD->setDado('cod_periodo_movimentacao', $arPeriodoMovimentacao['cod_periodo_movimentacao']);
                $this->obTExportacaoMANAD->setDado('cod_entidade', $codEntidade);
                $this->obTExportacaoMANAD->setDado('exercicio', $this->getExercicio());
                $this->obTExportacaoMANAD->recuperaDadosK050($$stArquivoK050);

                $this->arCountRegistros['K050'] += $$stArquivoK050->getNumLinhas();

                //SQL para pesquisa
                $obExportador->roUltimoArquivo->addBloco($$stArquivoK050);

                //01 - REG - Texto Fixo Contendo 'K050' (Caracter de tamanho 4).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                //02 - CNPJ/CEI - Numero de Inscricao do contribuinte no CNPJ ou no Cadastro Especifico do INSS (Numerico de tamanho variavel (12 ou 14)).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cei");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

                //03 - DT_INC_ALT - Representa a data de inclusao ou alteracao do cadastro, se houver, conforme item 3.1.10 (Numerico de tamanho 8).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inc_alt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                //04 - COD_REG_TRAB - Codigo que identifica o trabalhador na empresa e/ou estabelecimento (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reg_trab");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                //05 - CPF - CPF do trabalhador (Numerico de tamanho 11).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

                //06 - NIT - Numero de Identificacao do Trabalhador (PIS/PASEP/SUS) (Numerico de tamanho 11).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nit");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

                //07 - COD_CATEG - Categoria do trabalhador, conforme tabela constante do Manual da GFIP. (P/ estagiario informar: 00) (Numerico de tamanho 2)
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_categ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                //08 - NOME_TRAB - Nome completo do trabalhar (Numerico de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_trab");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //09 - DT_NASC - Data de nascimento do trabalhador, conforme item 3.1.5 (Numerico de tamanho 8).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_nasc");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                //10 - DT_ADMISSAO - Data de nascimento do trabalahdor, conforme item 3.1.5 (Numerico de tamanho 8).
                //                      - Contribuintes individuais (trabalhadores autonomos): deixar o campo vazio;
                //                      - Contribuintes individuais (empresarios): data do inicio da vinculacao à empresa;
                //                      - Servidores Publicos: data do inicio do exercicio;
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_admissao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                //11 - DT_DEMISSAO - Data de demissao do trabalhador, conforme item 3.1.5 (Numerico de tamanho 8).
                //                      - Contribuintes individuais (trabalhadores autonomos): deixar o campo vazio;
                //                      - Contribuintes individuais (empresarios): data do termino da vinculacao à empresa;
                //                      - Servidores Publicos: data do termino do exercicio;
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_demissao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                //12 - IND_VINC - Indicador do tipo de vinculo do trabalhador: (Numerico de tamanho 1)
                //                      1 - Vinculo normal
                //                      2 - Vinculo temporaio com reducao de contribuicoes/obrigacao
                //                      3 - Servidor Publico Efetivo Estatutario
                //                      4 - Servidor Publico Efetivo CLT
                //                      5 - Servidor Publico Comissionado
                //                      6 - Servidor Publico Cedido
                //                      7 - Agentes Politicos
                //                      8 - Contribuinte Individual
                //                      9 - Outros vinculos
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_vinc");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                //13 - TIPO_ATO_NOM - Indicativo do tipo de nomeacao: (Numerico de tamanho 1)
                //                      1 - Lei
                //                      2 - Decreto
                //                      3 - Portaria
                //                      4 - Contrato
                //                      9 - Outros
                //                    (Apenas para orgaos publicos. Direito Privado informar campo vazio)
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_ato_nom");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                //14 - NM_ATO_NOM - Numero do Ato de Nomeacao (Caracter de tamanho indefinido).
                //                  (Apenas para orgaos publicos, Direito Privado informar campo vazio)
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_ato_nom");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //15 - DT_ATO_NOM - Data do Ato de Nomeacao, conforme item 3.1.5 (Caracter de tamanho 1)
                //                      (Apenas para orgaos publicos, Direito Privado informar campo vazio)
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_ato_nom");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
            }
        }

        #######################################################################################################
        # Registro tipo K100
        # Lotacao
        #
        # Observacoes:
        #   Ocorrencia: varios
        #   (*) No caso de empresas prestadoras de servico mediante cessao de mao-de-obra ou empreiteiras contratadas por empreitada parcial na construcao civil, e
        #   obrigatorio informar como lotacoes, alem dos diversos departamentos administrativos, cada um dos estabelecadasdsaimentos tomadores de servicos ou obras.
        #######################################################################################################

        $this->arCountRegistros['K100'] = 0;
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            foreach ($arEsquemasEntidades as $codEntidade) {

                if ($codEntidadePrefeitura !=$codEntidade) {
                    $stEntidade = '_'.$codEntidade;
                } else {
                    $stEntidade = '';
                }

                $stArquivoK100 = "rsArquivoK100";
                $stArquivoK100 .= $stEntidade;
                $stArquivoK100 .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
                $$stArquivoK100 = new RecordSet();

                $this->obTExportacaoMANAD->setDado('entidade', $stEntidade);
                $this->obTExportacaoMANAD->setDado('cod_periodo_movimentacao', $arPeriodoMovimentacao['cod_periodo_movimentacao']);
                $this->obTExportacaoMANAD->setDado('codentidade', $codEntidade);
                $this->obTExportacaoMANAD->setDado('exercicio', $this->getExercicio());
                $this->obTExportacaoMANAD->setDado('dtInicial',$this->getDataInicial() );
                $this->obTExportacaoMANAD->recuperaDadosK100($$stArquivoK100);

                $this->arCountRegistros['K100'] += $$stArquivoK100->getNumLinhas();

                //SQL para pesquisa
                $obExportador->roUltimoArquivo->addBloco($$stArquivoK100);

                //01 - REG - Texto Fixo Contendo 'K100' (Caracter de tamanho 4).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                //02 - DT_INC_ALT - Representa a data de inclusao ou alteracao do cadastro da lotacao no sistema de folha da empresa, conforme item 3.1.10 (Numerico de tamanho 8).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inc_alt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                //03 - COD_LTC - Codigo da Lotacao conforme tabela de Lotacoes de responsabilidade do contribuinte. (Caracter de tamanho indefinido).
                //               A codificacao de lotacao deve permitir a informacao da folha de pagamento por departamentos.
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ltc");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //04 - CNPJ/CEI - Numero de Inscricao do contribuinte no CNPJ ou no Cadastro Especifico do INSS (Numerico de tamanho variavel (12 ou 14)).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cei");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //05 - DESC_LTC - Descricao da Lotacao. (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_ltc");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //06 - CNPJ/CEI_TOM - Preencher apenas no caso de prestadoras de servico com o numero de Inscricao do CNPJ/CEI do tomador (*) (Numerico de tamanho variavel (12 ou 14)).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cei_tom");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            }
        }

        #######################################################################################################
        # Registro tipo K150
        # Rubricas
        #
        # Observacoes:
        #   Ocorrencia: varios
        #######################################################################################################

        $this->arCountRegistros['K150'] = 0;
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            foreach ($arEsquemasEntidades as $codEntidade) {

                if ($codEntidadePrefeitura !=$codEntidade) {
                    $stEntidade = '_'.$codEntidade;
                } else {
                    $stEntidade = '';
                }

                $stArquivoK150 = "rsArquivoK150";
                $stArquivoK150 .= $stEntidade;
                $stArquivoK150 .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
                $$stArquivoK150 = new RecordSet();

                $this->obTExportacaoMANAD->setDado('entidade', $stEntidade);
                $this->obTExportacaoMANAD->setDado('cod_periodo_movimentacao', $arPeriodoMovimentacao['cod_periodo_movimentacao']);
                $this->obTExportacaoMANAD->recuperaDadosK150($$stArquivoK150);

                $this->arCountRegistros['K150'] += $$stArquivoK150->getNumLinhas();
                //SQL para pesquisa
                $obExportador->roUltimoArquivo->addBloco($$stArquivoK150);

                //01 - REG - Texto Fixo Contendo 'K150' (Caracter de tamanho 4).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                //02 - CNPJ/CEI - Numero de Inscricao do contribuinte no CNPJ ou no Cadastro Especifico do INSS (Numerico de tamanho variavel (12 ou 14)).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cei");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //03 - DT_INC_ALT - Representa a data de inclusao ou alteracao da rubrica no sistema de folha da empresa, conforme item 3.1.10 (Numerico de tamanho 8).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inc_alt");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                //04 - COD_RUBRICA - Codigo da Rubrica. (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_rubrica");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //05 - DESC_RUBRICA - Descricao da Rubrica. (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_rubrica");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            }
        }

        # ESTE REGISTRO NÃO SERÁ GERADO AGORA. CASO O VALIDADOR DO MANAD EXIJA ESTE REGISTRO, Ai ELE SERÁ GERADO

        // #######################################################################################################
        // # Registro tipo K200
        // # Contabilizacao da folha de pagamento
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #######################################################################################################

        // //SQL para pesquisa
        // $obExportador->roUltimoArquivo->addBloco($rsRegistrok200);

        // //01 - REG - Texto Fixo Contendo 'K200' (Caracter de tamanho 4).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        // //02 - DT_INC_ALT - Representa a data de inclusao ou alteracao da forma de contabilizacao, conforme item 3.1.10 (Numerico de tamanho 8).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inc_alt");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        // //03 - CNPJ/CEI - Numero de Inscricao do contribuinte no CNPJ ou no Cadastro Especifico do INSS (Numerico de tamanho variavel (12 ou 14)).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cei");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // //04 - COD_RUBRICA - Codigo da Rubrica. (Caracter de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_rubrica");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // //05 - COD_LTC - Codigo da Lotacao conforme tabela de Lotacoes de responsabilidade do contribuinte. (Caracter de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ltc");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // //06 - COD_CCUS - Codigo do centro de custos. (Caracter de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ccus");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // //07 - COD_CTA - Codigo da conta analitica debitada/creditada. (Caracter de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        #######################################################################################################
        # Registro tipo K250
        # Mestre de folha de pagamento
        #
        # Observacoes:
        #   Ocorrencia: varios
        #   - Mesmo que o trabalhador possua valor de base de calculo da previdencia social igual a zero na competencia,
        #     devera ser informado um registro no Mestre com o campo VL_BASE_PS igual a zero, quando constar algum registro
        #     no Itens de Folha (K300) para ele naquela competencia.
        #   - O codigo a ser informado no campo IND_FL refere-se aos diferentes processamentos efetuados no mes pelo sistema
        #     de folha da empresa. Exemplo: caso a folha normal do mes inclua os valores rescisorios (incluindo 13º salario)
        #     ou de ferias, as rubricas correspondentes devem ser informadas com IND_FL=1(folha normal). Entretanto, caso seja
        #     processada folha de pagamento especifica para as rescisoes, a mesma deve ser informada, por exemplo, com IND_FL=6 (outras folhas).
        #   - Deve haver coerẽncia entre os codigos do campo IND_FL usados nos registros K300  e sua correspondente totalizacao nos K250. Exemplo:
        #     o registro K250 totalizando a folha normal de um determinado trabalhador e os diversos K300 informando as diversas rubricas constantes
        #     daquela folha referente a este trabalhador devem guardar coerencia entre si.
        #   - (*) No caso de prestadores de servico, cada trabalahdo tera tantos registros no Mestre de Folha para a competencia
        #     quantas forem as lotacoes às quais ele preste servico no decorrer do mes.
        #######################################################################################################

        $this->arCountRegistros['K250'] = 0;
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            foreach ($arEsquemasEntidades as $codEntidade) {

                if ($codEntidadePrefeitura !=$codEntidade) {
                    $stEntidade = '_'.$codEntidade;
                } else {
                    $stEntidade = '';
                }

                $stArquivoK250 = "rsArquivoK250";
                $stArquivoK250 .= $stEntidade;
                $stArquivoK250 .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
                $$stArquivoK250 = new RecordSet();

                $this->obTExportacaoMANAD->setDado('entidade', $stEntidade);
                $this->obTExportacaoMANAD->setDado('exercicio', $this->getExercicio());
                $this->obTExportacaoMANAD->setDado('cod_entidade', $codEntidade);
                $this->obTExportacaoMANAD->setDado('cod_periodo_movimentacao', $arPeriodoMovimentacao['cod_periodo_movimentacao']);
                $this->obTExportacaoMANAD->recuperaDadosK250($$stArquivoK250);

                $this->arCountRegistros['K250'] += $$stArquivoK250->getNumLinhas();

                //SQL para pesquisa
                $obExportador->roUltimoArquivo->addBloco($$stArquivoK250);
                $obExportador->roUltimoArquivo->setTipoDocumento('manad');

                //01 - REG - Texto Fixo Contendo 'K250' (Caracter de tamanho 4).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                //02 - CNPJ/CEI - Numero de Inscricao do contribuinte no CNPJ ou no Cadastro Especifico do INSS (Numerico de tamanho variavel (12 ou 14)).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cei");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //03 - IND_FL - Indicador do tipo de Folha: (Numerico de tamanho 1).
                //                  1 - Folha normal
                //                  2 - Folha de 13º Salario
                //                  3 - Folha de Ferias
                //                  4 - Folha Complementar à normal
                //                  5 - Folha Complementar ao 13º
                //                  6 em diante - Outras folhas
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_fl");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                //04 - COD_LTC - Codigo da Lotacao conforme tabela de Lotacoes de responsabilidade do contribuinte. (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ltc");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //05 - COD_REG_TRAB - Codigo do registro do trabalahdor constante da tabela Cadastro de Trabalhadores. (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reg_trab");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                //06 - DT_COMP - Campo de Periodo conforme descrito no item 3.1.6. (Numerico de tamanho 6).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_comp");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                //07 - DT_PGTO - Campo referente à data da realizacao do pagamento. Conforme descrito no item 3.1.5. (Numerico de tamanho 8).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_pgto");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                //08- COD_CBO - Codigo Brasileiro de Ocupacao. (Numerico de tamanho 8).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cbo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                //09 - COD_OCORR - Codigo da ocorrencia cofnrome tabela existente no Manual da GFIP. (Numerico de tamanho 2).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ocorr");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                //10 - DESC_CARGO - Descricao do cargo do trabalhador . (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //11 - QTD_DEP_IR - Quantidade de dependentes para fins de Imposto de Renda - Pessoa Fisica. (Numerico de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_dep_ir");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

                //12 - QTD_DEP_SF - Quantidade de dependentes para fins de Salario Familia. (Numerico de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_dep_sf");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

                //13 - VL_BASE_IRRF - Base de Calculo para o Imposto de Renda - Pessoa Fisica. (Numerico de tamanho indefindo, e limitado a 2 casas decimais).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_base_irrf");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                //14 - VL_BASE_PS - Base de Calculo para a Previdencia Social. (Numerico de tamanho indefindo, e limitado a 2 casas decimais).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_base_ps");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            }
        }

        #######################################################################################################
        # Registro tipo K300
        # Itens de folha de pagamento
        #
        # Observacoes:
        #   Ocorrencia: varios
        #   - No caso do contribuinte individual freteiro (transportador), em que a remuneracao corresponde a um percentual
        #     definido pela Previdencia social aplicado sobre o valor bruto do frete, deve ser informado no campo VLR_RUBR
        #     o valor da remuneracao.
        #   - O codigo a ser informado no campo IND_FL refere-se aos diferentes processamentos efetuados no mes pelo sistema
        #     de folha da empresa. Exemplo: caso a folha normal do mes inclua os valores rescisorios (incluindo 13º salario)
        #     ou de ferias, as rubricas correspondentes devem ser informadas com IND_FL=1 (folha normal). Entretanto, caso seja
        #     processada folha de pagamento especifica para as rescisoes, a mesma deve ser informado, por exemplo, com IND_FL=6.
        #   - Deve haver coerencia entre os codigos do campo IND_FL usados no registros K300 e sua correspondente totalizacao nos
        #     K250. Exemplo: o registro K250 totalizando a folha normal de um determinado trabalhador e os diversos K300 informando
        #     as diversas rubricas constantes daquela oflha referente a este trabalhador devem guardar correspondencia.
        #   - O IND_BASE_PS = 9 (outras bases de calculo) deve ser usado para rubricas que, embora nao componham a base para Previdencia
        #     Social, integrem a base de caculo para outros efeitos (que nao o IRRF e o FGTS). Exemplo: no caso especifico de servidores
        #     publicos vinculados ao Regime Proprio da Previdencia Social, o IND_BASE_PS=9 deve usado para identificar as rubricas com
        #     incidencia para o respectivo regime.
        #   - (*) No caso de prestadoras de servico, deverao ser informadas as rubricas recebidas pelo trabalhador, na competencia,
        #     discriminadas por lotacao. Observar que cada registro no K300 devera possuir a correspondente totalizacao no tipo de registro
        #     K250, com a mesma chave de identificacao (campos CNPJ_CEI, IND_FL, COD_LTC, COD_REG_TRAB e DT_COMP).
        #######################################################################################################

        $this->arCountRegistros['K300'] = 0;
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            foreach ($arEsquemasEntidades as $codEntidade) {

                if ($codEntidadePrefeitura !=$codEntidade) {
                    $stEntidade = '_'.$codEntidade;
                } else {
                    $stEntidade = '';
                }

                $stArquivoK300 = "rsArquivoK300";
                $stArquivoK300 .= $stEntidade;
                $stArquivoK300 .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
                $$stArquivoK300 = new RecordSet();

                $this->obTExportacaoMANAD->setDado('entidade', $stEntidade);
                $this->obTExportacaoMANAD->setDado('cod_periodo_movimentacao', $arPeriodoMovimentacao['cod_periodo_movimentacao']);
                $this->obTExportacaoMANAD->recuperaDadosK300($$stArquivoK300);

                $this->arCountRegistros['K300'] += $$stArquivoK300->getNumLinhas();

                $obExportador->roUltimoArquivo->addBloco($$stArquivoK300);
                $obExportador->roUltimoArquivo->setTipoDocumento('manad');
                //01 - REG - Texto Fixo Contendo 'K300' (Caracter de tamanho 4).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                //02 - CNPJ/CEI - Numero de Inscricao do contribuinte no CNPJ ou no Cadastro Especifico do INSS (Numerico de tamanho variavel (12 ou 14)).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cei");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //03 - IND_FL - Indicador do tipo de Folha: (Numerico de tamanho 1).
                //                  1 - Folha normal
                //                  2 - Folha de 13º Salario
                //                  3 - Folha de Ferias
                //                  4 - Folha Complementar à normal
                //                  5 - Folha Complementar ao 13º
                //                  6 em diante - Outras folhas
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_fl");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                //04 - COD_LTC - Codigo da Lotacao conforme tabela de Lotacoes de responsabilidade do contribuinte. (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ltc");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //05 - COD_REG_TRAB - Codigo do registro do trabalhador constante da tabela Cadastro de Trabalhadores. (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reg_trab");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                //06 - DT_COMP - Campo de Periodo conforme descrito no item 3.1.6. (Numerico de tamanho 6).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_comp");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                //07 - COD_RUBR - Codigo da Rubrica conforme a tabela de Rubricas. (reg. tipo K150) (Caracter de tamanho indefinido).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_rubr");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

                //08 - VLR_RUBR - Valor da Rubrica. (Numerico de tamanho indefinido, com 2 casas decimais).
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlr_rubr");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                //09 - IND_RUBR - Indicacao de Provento ou Desconto: (Caracter de tamanho 1).
                //                  D - Desconto
                //                  P - Provento ou Vantagem
                //                  O - Outros
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_rubr");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                //10 - IND_BASE_IRRF - Indicador de Base de Calculo para o Imposto de Renda - Pessoa Fisica: (Numerico de tamanho 1).
                //                  1 - Base de Calculo Salario Mensal
                //                  2 - Base de Calculo 13º Salario
                //                  3 - Nao e base de Calculo
                //                  9 - Outras bases de Calculo.
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_base_irrf");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                //11 - IND_BASE_PS - Indicador de Base de Calculo para a Previdencia Social: (Numerico de tamanho 1).
                //                  1 - Base de Calculo do Salario de Contribuicao mensal
                //                  2 - Base de Calculo do 13º Salario
                //                  3 - Refere--se a valor descontado do segurado
                //                  4 - Refere-se a valor pago de Salario Familia
                //                  5 - Base de Calculo do Salario-Maternidade
                //                  6 - Base de Calculo exclusiva do FGTS
                //                  7 - Reducoes da Base de Calculo
                //                  8 - Nao e Base de Calculo
                //                  9 - Outras bases de Caĺculo
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_base_ps");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
            }
        }

        #######################################################################################################
        # Registro tipo K990
        # Encerramento do bloco K
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #   A quantidade total de linhas do bloco K devera levar em consideracao todos os registros entre o primeiro K000 e o K990, inclusive.
        #######################################################################################################

        $arRegistroK990 = array( 0 => array(
            'reg' => 'K990',
            'qtd_lin_k' => $this->getNumLinhas('K')+1,
        ));

        $rsRegistroK990 = new RecordSet;
        $rsRegistroK990->preenche($arRegistroK990);

        $this->arCountRegistros['K990'] = $rsRegistroK990->getNumLinhas();
        $obExportador->roUltimoArquivo->addBloco($rsRegistroK990);

        //01 - REG - Texto Fixo Contendo 'K990' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - QTD_LIN_K - Quantidade total de linhas do Bloco K (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_lin_k");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    }

    /**
     * Bloco obrigatorio às empresas definidas no item 1.7 - Direito Publico
     *
     * Registro de natureza financeira e orcamentaria
     *
     * a) Os Restos a Pagar deverao constar em todos os arquivos a que eles se referem (empenho, liquidacao, pagamento, balancete de verificacao).
     * b) Os arquivos relativos ao Empenho deverao trazer, em sua composicao, todos os Restos a pagar (valor original) que ainda apresentem
     *    movimentacao ou saldo na data do encerramento do exercicio.
     * c) Para cada movimentacao ocorrida no empenho, liquidacao e pagamento atraves de credito adicional, anulacao ou cancelamento, devera ocorrer
     *    o acrescimo de um registro.
     *
     * Bloco L - Folha Pagamento
     */
    public function geraBlocoL(&$obExportador)
    {
        #######################################################################################################
        # Registro tipo L001
        # Abertura do bloco L
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #######################################################################################################

        $arRegistroL001 = array( 0 => array (
            'reg' => 'L001',
            'ind_mov' => 0
        ));

        $rsRegistroL001 = new RecordSet;
        $rsRegistroL001->preenche($arRegistroL001);

        $this->arCountRegistros['L001'] = $rsRegistroL001->getNumLinhas();

        //SQL para pesquisa
        $obExportador->roUltimoArquivo->addBloco($rsRegistroL001);

        //01 - REG - Texto Fixo Contendo 'K990' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - IND_MOV - Indicador de movimento: (Numerico de tamanho 1)
        //                  0 - Bloco com dados informados;
        //                  1 - Bloco sem dados informados;
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_mov");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        #######################################################################################################
        # Registro tipo L050
        # Empenho de despesas
        #
        # Observacoes:
        #   Ocorrencia: varios
        #######################################################################################################

        //SQL para pesquisa
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho();
        $obTEmpenhoEmpenho->setDado('stExercicio'   , $this->getExercicio());
        $obTEmpenhoEmpenho->setDado('dtInicial'     , $this->getDataInicial());
        $obTEmpenhoEmpenho->setDado('dtFinal'       , $this->getDataFinal());
        $obTEmpenhoEmpenho->setDado('stCodEntidades', $this->getCodEntidades());
        $obTEmpenhoEmpenho->recuperaDadosMANAD($rsRegistroL050);

        $this->arCountRegistros['L050'] = $rsRegistroL050->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistroL050);

        //01 - REG - Texto Fixo Contendo 'L050' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - COD_ORG - Codigo do orgao (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_org");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

        //03 - COD_UN_ORC - Codigo da Unidade Orcamentario (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_un_orc");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

        //04 - COD_FUN - Codigo da Funcao Conforme Lei 4.320/64 e Portaria 42/MPOG (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fun");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        //05 - COD_SUBFUN - Codigo da Subfuncao conforme Portaria 42/MPOG (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfun");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        //06 - COD_PROGR - Codigo do Programa conforme Lei 4.320/64 (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_progr");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //07 - COD_SUBPROGR - Codigo do Subprograma Lei 4.320/64 (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subprogr");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //08 - COD_PROJ_ATIV_OE - Codigo do Proejtos, Atividades e Operacoes Especiais. Codificacao propria. (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_proj_ativ_oe");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //09 - COD_CTA_DESP - Codigo de Rubrica da Despesa (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta_desp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        //10 - COD_REC_VINC - Codigo do Recurso Vinculado (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_rec_vinc");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //11 - COD_CONT_REC - Codigo de Contrapartida do Recurso (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cont_rec");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //12 - NM_EMP - Numero do Empenho (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_emp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //13 - DT_EMP - Data do Empenho (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //14 - VL_EMP - Valor do empenho (Numerico de tamanho indefinido, com 2 casas decimais).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_emp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //15 - IND_DEB_CRED - Indicativo de debito ou credito do valor conforme: (Caracter de tamanho 1).
        //                      D - Empenho originario e credito adicial
        //                      C - Anulacao, Cancelamento
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_deb_cred");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        //16 - COD_CREDOR - Codigo do Credor (Caracter de tamanho 1).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_credor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        //17 - HIST_EMP - Historico do Empenho (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hist_emp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        #######################################################################################################
        # Registro tipo L100
        # Liquidacao do Empenho
        #
        # Observacoes:
        #   Ocorrencia: varios
        #######################################################################################################

        //SQL para pesquisa
        $obFExportacaoLiquidacao = new FExportacaoLiquidacao();
        $obFExportacaoLiquidacao->setDado('stExercicio'   , $this->getExercicio());
        $obFExportacaoLiquidacao->setDado('dtInicial'     , $this->getDataFinal());
        $obFExportacaoLiquidacao->setDado('dtFinal'       , $this->getDataFinal());
        $obFExportacaoLiquidacao->setDado('stCodEntidades', $this->getCodEntidades());
        $obFExportacaoLiquidacao->setDado('stFiltro'      , "");
        $obErro = $obFExportacaoLiquidacao->recuperaDadosMANAD($rsRegistroL100);

        $this->arCountRegistros['L100'] = $rsRegistroL100->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistroL100);

        //01 - REG - Texto Fixo Contendo 'L100' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - NM_EMP - Numero do Empenho (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_emp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //03 - NM_LIQUID - Numero da Liquidacao (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_liquid");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //04 - DT_LIQUID - Data da Liquidacao (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_liquid");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //05 - VL_LIQUID - Valor da Liquidacao (R$) (Numerico de tamanho indefinido, com 2 casas decimais).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquid");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //06 - IND_DEB_CRED - Indicativo de debito ou credito do valor conforme: (Caracter de tamanho 1).
        //                      D - Liquidacao originaria e credito adicial
        //                      C - Anulacao, Cancelamento
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_deb_cred");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        //07 - HIST_LIQUID - Historico da Liquidacao (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hist_liquid");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        #######################################################################################################
        # Registro tipo L150
        # Pagamento do Empenho
        #
        # Observacoes:
        #   Ocorrencia: varios
        #######################################################################################################

        $obFExportacaoPagamento = new FExportacaoPagamento();
        $obFExportacaoPagamento->setDado('stExercicio'   , $this->getExercicio());
        $obFExportacaoPagamento->setDado('dtInicial'     , $this->getDataInicial());
        $obFExportacaoPagamento->setDado('dtFinal'       , $this->getDataFinal());
        $obFExportacaoPagamento->setDado('stCodEntidades', $this->getCodEntidades());
        $obErro = $obFExportacaoPagamento->recuperaDadosMANAD($rsRegistroL150);

        $this->arCountRegistros['L150'] = $rsRegistroL150->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistroL150);

        //01 - REG - Texto Fixo Contendo 'L150' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - NM_EMP - Numero do Empenho (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_emp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //03 - NM_PGTO - Numero do Pagamento (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_pgto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //04 - DT_PGTO - Data de Pagamento (Numerico de tamanho 8).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_pgto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        //05 - VL_PGTO - Valor do Pagamento (R$) (Numerico de tamanho indefinido, com 2 casas decimais).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pgto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //06 - IND_DEB_CRED - Indicativo de debito ou credito do valor conforme: (Caracter de tamanho 1).
        //                      D - Liquidacao originaria e credito adicial
        //                      C - Anulacao, Cancelamento
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_deb_cred");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        //07 - HIST_PGTO - Historico do Pagamento (Caracter de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hist_pgto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        //08 - CTA_DEBITO - Codigo da Conta do balancete de verificacao lancado a debito (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cta_debito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        //09 - COD_ORG_UN_DEB - Codigo do orgao + unidade orcamentaria do lancamento a debito (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_org_un_deb");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        //10 - CTA_CREDITO - Codigo da Conta do balancete de verificacao lancado a credito (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cta_credito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        //11 - COD_ORG_UN_CRE - Codigo do orgao + unidade orcamentaria do lancamento a credito (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_org_un_cre");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        // #######################################################################################################
        // # Registro tipo L200
        // # Balancete da receita
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #######################################################################################################

        // //SQL para pesquisa
        $arrEntidades = explode(',', $this->getCodEntidades());
        foreach ($arrEntidades as $stEntidade) {
            $inCodPrefeitura = SistemaLegado::pegaConfiguracao('cod_entidade_prefeitura',8,$this->getExercicio());
            $inCodCamara = SistemaLegado::pegaConfiguracao('cod_entidade_camara',8,$this->getExercicio());
            $inCodRPPS = SistemaLegado::pegaConfiguracao('cod_entidade_rpps',8,$this->getExercicio());

            if ($stEntidade == $inCodPrefeitura) {
                $inCodOrgao = SistemaLegado::pegaConfiguracao('manad_orgao_prefeitura',59,$this->getExercicio());
                $inCodUnidade = SistemaLegado::pegaConfiguracao('manad_unidade_prefeitura',59,$this->getExercicio());
            } elseif ($stEntidade == $inCodCamara) {
                $inCodOrgao =SistemaLegado::pegaConfiguracao('manad_orgao_camara',59,$this->getExercicio());
                $inCodUnidade = SistemaLegado::pegaConfiguracao('manad_unidade_camara',59,$this->getExercicio());
            } elseif ($stEntidade ==$inCodRPPS) {
                $inCodOrgao = SistemaLegado::pegaConfiguracao('manad_orgao_rpps',59,$this->getExercicio());
                $inCodUnidade = SistemaLegado::pegaConfiguracao('manad_unidade_rpps',59,$this->getExercicio());
            } else {
                $inCodOrgao = SistemaLegado::pegaConfiguracao('manad_orgao_outros',59,$this->getExercicio());
                $inCodUnidade = SistemaLegado::pegaConfiguracao('manad_unidade_outros',59,$this->getExercicio());
            }

            $inCodOrgao = str_pad($inCodOrgao,5 , "0", STR_PAD_LEFT);
            $inCodUnidade=  str_pad($inCodUnidade,5, "0", STR_PAD_LEFT);

            $obFOrcamentoBalanceteReceita = new FOrcamentoBalanceteReceita();
            $obFOrcamentoBalanceteReceita->setDado('orgao', $inCodOrgao);
            $obFOrcamentoBalanceteReceita->setDado('unidade', $inCodUnidade);
            $obFOrcamentoBalanceteReceita->setDado('stExercicio'   , $this->getExercicio());
            $obFOrcamentoBalanceteReceita->setDado('dtInicial'     , $this->getDataInicial());
            $obFOrcamentoBalanceteReceita->setDado('dtFinal'       , $this->getDataFinal());
            $obFOrcamentoBalanceteReceita->setDado('stCodEntidades', $this->getCodEntidades());

            $obFOrcamentoBalanceteReceita->recuperaDadosMANAD($rsRegistroL200);

            $this->arCountRegistros['L200'] = $rsRegistroL200->getNumLinhas();
            $obExportador->roUltimoArquivo->addBloco($rsRegistroL200);

             //01 - REG - Texto Fixo Contendo 'L200' (Caracter de tamanho 4).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
             $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

             //02 - EXERC - Exercicio a que se refere a receita (Numerico de tamanho 4).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

             //03 - COD_CTA_RECEITA - Codigo da Conta de Receita (Numerico de tamanho indefinido).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta_receita");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

             //04 - COD_ORG_UN_ORC - orgao + Unidade Orcamentaria (Numerico de tamanho indefinido).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_org_un_orc");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

             //05 - VL_REC_ORCADA - Receita Orcada no Exercicio (Numerico de tamanho indefinido, com 2 casas decimais).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_orcada");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

             //06 - VL_REC_REALIZADA - Receita Realizada no Periodo (Numerico de tamanho indefinido, com 2 casas decimais).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_realizada");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            //07 - COD_REC_VINC - Codigo do Recurso Vinculado (Numerico de tamanho indefinido).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_rec_vinc");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

             //08 - DESC_RECEITA - Especificacao da Conta de Receita (Caracter de tamanho indefinido).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_receita");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
             //09 - IND_TIPO_CONTA - Tipo do Nivel da Conta: (Caracter de tamanho 1).
             //                      S - Sintetica
             //                      A - Analitica
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_tipo_conta");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

             //10 - NM_NIVEL_CONTA - Numero do nivel da Conta (Numerico de tamanho indefinido).
             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_nivel_conta");
             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        }

        // #######################################################################################################
        // # Registro tipo L250
        // # Balancete da despesa
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #######################################################################################################

        // //SQL para pesquisa

         $this->obFExportacaoBalanceteDespesa->setDado('stExercicio'    , $this->getExercicio()        );
         $this->obFExportacaoBalanceteDespesa->setDado('dtInicial'      , $this->getDataInicial()      );
         $this->obFExportacaoBalanceteDespesa->setDado('dtFinal'        , $this->getDataFinal()        );
         $this->obFExportacaoBalanceteDespesa->setDado('stCodEntidades' , $this->getCodEntidades()     );
         $obErro =   $this->obFExportacaoBalanceteDespesa->recuperaDadosMANAD($rsRegistroL250       );

          $this->arCountRegistros['L250'] = $rsRegistroL250->getNumLinhas();
         $obExportador->roUltimoArquivo->addBloco($rsRegistroL250);

         //01 - REG - Texto Fixo Contendo 'L250' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio a que se refere a receita (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_ORG - Codigo do orgao (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_org");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

         //04 - COD_UN_ORC - Codigo da Unidade Orcamentario (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_un_orc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

         //05 - COD_FUN - Codigo da Funcao Conforme Lei 4.320/64 e Portaria 42/MPOG (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fun");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

         //06 - COD_SUBFUN - Codigo da Subfuncao conforme Portaria 42/MPOG (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfun");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

         //07 - COD_PROGR - Codigo do Programa conforme Lei 4.320/64 (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_progr");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //08 - COD_SUBPROGR - Codigo do Subprograma Lei 4.320/64 (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subprogr");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //09 - COD_PROJ_ATIV_OE - Codigo do Proejtos, Atividades e Operacoes Especiais. Codificacao propria. (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_proj_ativ_oe");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //10 - COD_SUBELEMENTO - Codigo do Subelemento previsto na Lei 4.320/64 (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subelemento");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

         //11 - COD_CTA_DESP - Codigo de Rubrica da Despesa (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta_desp");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

         //12 - COD_REC_VINC - Codigo do Recurso Vinculado (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_rec_vinc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //13 - VL_DOTACAO_INICIAL - Valor da Dotacao Inicial - Fixada (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_dotacao_inicial");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //14 - VL_AT_MONETARIA - Valor da Atualizacao Monetaria (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_at_monetaria");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //15 - VL_CRED_SUP - Valor de Creditos Suplementares (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cred_sup");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //16 - VL_CRED_ESP - Valor de Credito Especiais (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cred_esp");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //17 - VL_CRED_EXT - Valor de Creditos Extraordinarios (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cred_ext");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        //18 - VL_RED_DOTACAO - Valor da Reducao das dotacoes (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_red_dotacao");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //19 - VL_SUP_REC_VINC - Valor da Suplementacao por recurso vinculado (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_sup_rec_vinc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //20 - VL_RED_REC_VINC - Valor da Reducao por recurso vinculado (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_red_rec_vinc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //21 - VL_EMPENHADO - Valor Empenhado (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_empenhado");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //22 - VL_LIQUIDADO - Valor Liquidado (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquidado");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //23 - VL_PAGO - Valor Pago (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //24 - VL_LMTDO_LRF - Valor Limitado pela Lei de Responsabilidade Fiscal (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_lmtdo_lrf");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        // #######################################################################################################
        // # Registro tipo L300
        // # Alteracoes da Lei do Orcamento
        // #
        // # Observacoes:
        // #   - Registros contendo as informacoes referentes às alteracoes realizadas na Lei do Orcamento tais como creditos adicionais,
        // #     cancelamentos de dotacoes.
        // #   Ocorrencia: varios
        // #######################################################################################################

         //SQL para pesquisa

         $this->obTExportacaoMANAD->setDado('stExercicio'        , $this->getExercicio()        );
         $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
         $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );
         $obErro =   $this->obTExportacaoMANAD->recuperaDadosL300($rsRegistroL300);
         if ($rsRegistroL300->getNumLinhas() < 0) {
           $this->arCountRegistros['L300'] = 0;
        } else {
            $this->arCountRegistros['L300'] = $rsRegistroL300->getNumLinhas();
        }

         $obExportador->roUltimoArquivo->addBloco($rsRegistroL300);

         //01 - REG - Texto Fixo Contendo 'L300' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - NM_LEI_DECRETO - Numero da Lei ou Decreto (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_lei_decreto");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

         //03 - DT_LEI_DECRETO - Data da Lei ou Decreto (Numerico de tamanho 8).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lei_decreto");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

         //04 - VL_CRED_ADICIONAL - Valor do Credito Adicional (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cred_adicional");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //05 - VL_RED_DOTACOES - Valor da Reducao de Dotacoes (Numerico de tamanho indefinido, com 2 casas decimais).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_red_dotacoes");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

         //06 - TIP_CRED_ADICIONAL - Tipo de Credito Adicional: (Numerico de tamanho 1).
         //                              1 - Suplementar
         //                              2 - Especial
         //                              3 - Extraordinario
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tip_cred_adicional");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

         //07 - TIP_ORIG_RECURSO - Origem dos Recursos: (Numerico de tamanho 1).
         //                          1 - Superavit Financeiro
         //                          2 - Excesso de Arrecadacao
         //                          3 - Operacoes de Credito
         //                          4 - Auxilios Convenios
         //                          5 - Reducoes Orcamentarias
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tip_orig_recurso");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        // #######################################################################################################
        // # Registro tipo L350
        // # orgao
        // #
        // # Observacoes:
        // #   - Registros contendo as informacoes referentes aos orgaos.
        // #   Ocorrencia: varios
        // #######################################################################################################

         //SQL para pesquisa
         $this->obTExportacaoMANAD->setDado('stExercicioLogado'    , Sessao::getExercicio()  );
         $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
         $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );
         $this->obTExportacaoMANAD->recuperaDadosL350($rsRegistroL350);

         $this->arCountRegistros['L350'] = $rsRegistroL350->getNumLinhas();
         $obExportador->roUltimoArquivo->addBloco($rsRegistroL350);

         //01 - REG - Texto Fixo Contendo 'L350' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_ORG - Codigo do orgao (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_org");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

         //04 - NOME_ORG - Nome do orgao (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_org");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // #######################################################################################################
        // # Registro tipo L400
        // # Unidade orcamentaria
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes às unidades orcamentarias.
        // #   - Todas as unidades orcamentarias deverao estar ligadas a um ente cadastrado no CNPJ.
        // #######################################################################################################

         //SQL para pesquisa
        $this->obTExportacaoMANAD->setDado('stExercicioLogado'    , Sessao::getExercicio()  );

        $identificador = '';
        $tamanhoEntidasSelecionadas = count($arFiltroRelatorio['arEntidadesSelecionadas']);

        if ($tamanhoEntidasSelecionadas <= 1) {
            $pegadado = SistemaLegado::pegaDado("parametro","administracao.configuracao","where parametro ilike 'cod_entidade_%' and exercicio = ".Sessao::getExercicio()." and valor in (".$this->getCodEntidades().");");

              if ($pegadado ==  'cod_entidade_prefeitura') {
                $identificador.= '1,3,4,6,7,8,9,10,11,12';
              }

              if ($pegadado == 'cod_entidade_camara') {
                $identificador.='2';
              }

              if ($pegadado == 'cod_entidade_rpps') {
                $identificador.='5';
              }

         } else {
              $pegadado1 = SistemaLegado::pegaDado("parametro","administracao.configuracao","where parametro ilike 'cod_entidade_prefeitura' and exercicio = '".Sessao::getExercicio()."'");
              $pegadado2 = SistemaLegado::pegaDado("parametro","administracao.configuracao","where parametro ilike 'cod_entidade_rpps' and exercicio = '".Sessao::getExercicio()."'");
                        if ( ($pegadado2 == 'cod_entidade_rpps') && ($pegadado1 == 'cod_entidade_prefeitura') ) {
                              $identificador.='1,3,4,5,6,7,8,9,10,11,12';
                        }
         }

         //verifica identificador não vazio e seta os dados
         if ($identificador != '') {
              $stFiltroEntidade = $identificador;
              $this->obTExportacaoMANAD->setDado('identificador',$stFiltroEntidade);
         }
        $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
        $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
        $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );
        $this->obTExportacaoMANAD->recuperaDadosL400($rsRegistroL400);

        $this->arCountRegistros['L400'] = $rsRegistroL400->getNumLinhas();

         $obExportador->roUltimoArquivo->addBloco($rsRegistroL400);

         //01 - REG - Texto Fixo Contendo 'L400' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_ORG - Codigo do orgao (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_org");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

         //04 - COD_UN_ORC - Codigo da Unidade Orcamentaria (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_un_orc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

         //05 - NOM_UN_ORC - Nome da Unidade Orcamentaria (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_un_orc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

         //06 - TIP_UN_ORC - Tipo de Unidade Orcamentaria: (Numerico de tamanho 2).
         //                          01 - Prefeitura Municipal
         //                          02 - Câmara Municipal
         //                          03 - Secretaria de Educacao
         //                          04 - Secretaria da Saude
         //                          05 - RPPS (exceto Autarquia)
         //                          06 - Autarquia (exceto RPPS)
         //                          07 - Autarquia (RPPS)
         //                          08 - Fundacao
         //                          09 - Empresa Estatal Dependente
         //                          10 - Empresa Estatal nao Dependente
         //                          11 - Consorcio
         //                          12 - Outras
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tip_un_orc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

         //07 - CNPJ - Numero do Cadastro da Unidade Orcamentaria no CNPJ. (Numerico de tamanho 14).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        // #######################################################################################################
        // # Registro tipo L450
        // # Funcao
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes as funcoes definidas nos moldes da Lei 4.320/64 e da Portaria 42/MPOG
        // #######################################################################################################

        // //SQL para pesquisa
         $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio() );
         $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
         $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );

         $this->obTExportacaoMANAD->recuperaDadosL450($rsRegistroL450);

         $this->arCountRegistros['L450'] = $rsRegistroL450->getNumLinhas();

         $obExportador->roUltimoArquivo->addBloco($rsRegistroL450);

         //01 - REG - Texto Fixo Contendo 'L450' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_FUN - Codigo da Funcao Conforme Lei 4.320/64 e Portaria 42/MPOG (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fun");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

         //04 - NOME_FUN - Descricao do nome da funcao (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_fun");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // #######################################################################################################
        // # Registro tipo L500
        // # SubFuncao
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes às subfuncoes definidas nos moldes da Lei 4.320/64 e da Portaria 42/MPOG
        // #######################################################################################################

         //SQL para pesquisa
         $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio() );
         $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
         $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );
         $this->obTExportacaoMANAD->recuperaDadosL500($rsRegistroL500);

         $this->arCountRegistros['L500'] = $rsRegistroL500->getNumLinhas();
         $obExportador->roUltimoArquivo->addBloco($rsRegistroL500);

         //01 - REG - Texto Fixo Contendo 'L500' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_SUBFUN - Codigo da SubFuncao Conforme Lei 4.320/64 e Portaria 42/MPOG (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfun");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
           $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

         //04 - NOME_SUBFUN - Descricao do nome da SubFuncao (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_subfun");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // #######################################################################################################
        // # Registro tipo L550
        // # Programas
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes às programas definidas nos moldes da Lei 4.320/64.
        // #######################################################################################################

         //SQL para pesquisa
         $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio() );
         $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
         $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );
         $this->obTExportacaoMANAD->recuperaDadosL550($rsRegistroL550);
         $this->arCountRegistros['L550'] = $rsRegistroL550->getNumLinhas();

         $obExportador->roUltimoArquivo->addBloco($rsRegistroL550);

         //01 - REG - Texto Fixo Contendo 'L550' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_PROGR - Codigo do Programa Conforme Lei 4.320/64 (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_progr");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //04 - NOM_PROGR - Descricao do nome do Programa (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_progr");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // #######################################################################################################
        // # Registro tipo L600
        // # SubProgramas
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes às subprogramas definidas nos moldes da Lei 4.320/64.
        // #######################################################################################################

        // //SQL para pesquisa
         $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio() );
         $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
         $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );

         $this->obTExportacaoMANAD->recuperaDadosL600($rsRegistroL600);
         $this->arCountRegistros['L600'] = $rsRegistroL600->getNumLinhas();

         $obExportador->roUltimoArquivo->addBloco($rsRegistroL600);

         //01 - REG - Texto Fixo Contendo 'L600' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_SUBPROGR - Codigo do SubPrograma Conforme Lei 4.320/64 (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subprogr");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //04 - NOM_SUBPROGR - Descricao do nome do SubPrograma (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_subprogr");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // #######################################################################################################
        // # Registro tipo L650
        // # Projetos e Atividades
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes aos projetos e atividades.
        // #######################################################################################################

         //SQL para pesquisa
         $arEntidades= explode(',', $this->getCodEntidades() );
         foreach ($arEntidades as $codEntidade) {

            $stArquivoL650 = "rsArquivoL650";
            $stArquivoL650 .= $codEntidade;
            $$stArquivoL650 = new RecordSet();
            $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio());
            $this->obTExportacaoMANAD->setDado('stCodEntidade' ,  $codEntidade);
            $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
            $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );

            $this->obTExportacaoMANAD->recuperaDadosL650($$stArquivoL650 );

            $this->arCountRegistros['L650'] = $$stArquivoL650 ->getNumLinhas();

            $obExportador->roUltimoArquivo->addBloco($$stArquivoL650 );

            //01 - REG - Texto Fixo Contendo 'L600' (Caracter de tamanho 4).
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

            //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

            //03 - COD_PROJ_ATIV_OE - Codigo dos Projetos, Atividades e Operacoes Especiais. Codificacao Propria (Numerico de tamanho indefinido).
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_proj_ativ_oe");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

            //04 - NOM_PROJ_ATIV_OE - Descricao do nome do Projeto, Atividade ou Operacoes Especiais (Caracter de tamanho indefinido).
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_proj_ativ_oe");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

            //05 - TIP_PROJ_ATIV_OE - Tipificacao do Projeto, Atividade ou Operacoes Especiais: (Caracter de tamanho 2).
            //                          01 - RPPS
            //                          02 - Demais Projetos/Atividades/Operacoes Especiais
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tip_proj_ativ_oe");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
         }
        // #######################################################################################################
        // # Registro tipo L700
        // # Rubrica
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes às rubricas de despesa.
        // #######################################################################################################

         //SQL para pesquisa
         $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio() );
         $this->obTExportacaoMANAD->setDado('dtInicial'             , $this->getDataInicial()      );
         $this->obTExportacaoMANAD->setDado('dtFinal'               , $this->getDataFinal()        );
         $this->obTExportacaoMANAD->setDado('stCodEntidades' , $this->getCodEntidades()     );
         $this->obTExportacaoMANAD->recuperaDadosL700($rsRegistroL700);

         $this->arCountRegistros['L700'] = $rsRegistroL700->getNumLinhas();

         $obExportador->roUltimoArquivo->addBloco($rsRegistroL700);

         //01 - REG - Texto Fixo Contendo 'L700' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_CTA_DESP - Codigo de Rubrica da Despesa (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta_desp");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

         //04 - NOM_DESPESA - Especificacao da Rubrica de Despesa-SG (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_despesa");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

         //05 - IND_TIPO_CONTA - Tipo do Nivel da Conta: (Caracter de tamanho 1).
         //                          S - Sintetica
         //                          A - Analitica
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_tipo_conta");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

         //06 - NM_NIVEL_CONTA - Numero do nivel da Conta (Numerico de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_nivel_conta");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        // #######################################################################################################
        // # Registro tipo L750
        // # Fornecedores
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as informacoes referentes aos fornecedores.
        // #######################################################################################################

        //SQL para pesquisa
        $obTExportacaoConfiguracao = new TExportacaoMANADConfiguracao;
        $obRExportacaoMANAD = new RExportacaoMANAD;
        $obTExportacaoConfiguracao->setDado("parametro",  "manad_documento_inss_fornecedor");
        $obTExportacaoConfiguracao->setDado("cod_modulo", 59);
        $obTExportacaoConfiguracao->consultar();

        $dtFinal = '31/12/'.Sessao::getExercicio();

        $this->obTExportacaoMANAD->setDado('stExercicioLogado'   , Sessao::getExercicio());
        $this->obTExportacaoMANAD->setDado('dtInicial'     , $this->getDataInicial());
        $this->obTExportacaoMANAD->setDado('dtFinal'       , $dtFinal);
        $this->obTExportacaoMANAD->setDado('stCodEntidades', $this->getCodEntidades());
        $this->obTExportacaoMANAD->setDado('stDocINSS', (int) $obTExportacaoConfiguracao->getDado("valor"));
        $obErro = $this->obTExportacaoMANAD->recuperaDadosL750($rsRegistrol750);

        $this->arCountRegistros['L750'] = $rsRegistrol750->getNumLinhas();

         $obExportador->roUltimoArquivo->addBloco($rsRegistrol750);

         //01 - REG - Texto Fixo Contendo 'L750' (Caracter de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
         $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

         //03 - COD_FORNECEDOR - Codigo do Fornecedor (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

         //04 - NOM_FORNECEDOR - Nome do Fornecedor (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(150);

         //05 - TIP_FORNECEDOR - Tipificacao dos Fornecedores: (Numerico de tamanho 1).
         //                          01 - Pessoa Fisica
         //                          02 - Pessoa Juridica
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

         //06 - CNPJ_FORNECEDOR - Numero do CNPJ dos Fornecedores, se houver. Obrigatorio para as Pessoas Juridicas (Numericos de tamanho 14).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

         //07 - CPF_FORNECEDOR - Numero do CPF do Fornecedor, se houver. Obrigatorio para as Pessoas Fisicas (Numericos de tamanho 11).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

         //08 - NIT_FORNECEDOR - Numero do Trabalhador junto ao INSS (PIS/PASEP/SUS/CI). Obrigatorio para os Contribuintes Individuais (Numericos de tamanho 11).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nit_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

         //09 - END_FORNECEDOR - Endereco do Fornecedor (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("end_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(150);

         //10 - CID_FORNECEDOR - Cidade do Fornecedor (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cid_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

         //11 - UF_FORNECEDOR - Unidade da Federacao (Caracter de tamanho 2).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

         //12 - CEP_FORNECEDOR - Numero do CEP conforme cadastro dos Correios (Numerico de tamanho 8).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep_fornecedor");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

         //13 - DESC_TIP_FORN - Descricao do tipo de fornecimento prestado pelo Fornecedor (Caracter de tamanho indefinido).
         $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_tip_forn");
         $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // #######################################################################################################
        // # Registro tipo L800
        // # Obras da construcao civil e outros servicos sujeitos a retencao
        // #
        // # Observacoes:
        // #   Ocorrencia: varios
        // #   - Registros contendo as obras de construcao civil e outros servicos sujeitos à retencao a serem realizados
        // #     pelo ente orcamentario, conforme previsto na execucao da receita/despesa, conforme legislacao vigente.
        // #######################################################################################################

        // //SQL para pesquisa
        // $obExportador->roUltimoArquivo->addBloco($rsRegistrol800);

        // //01 - REG - Texto Fixo Contendo 'L800' (Caracter de tamanho 4).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        // //02 - EXERC - Exercicio financeiro e orcamentario (Numerico de tamanho 4).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exerc");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        // //03 - COD_CTA_DESP - Codigo de Rubrica da Despesa (Numerico de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cta_desp");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        // //04 - COD_FORNECEDOR - Codigo do Fornecedor (Caracter de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fornecedor");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // //05 - NM_EMP - Numero do Empenho (Caracter de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nm_emp");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");

        // //06 - TIP_OBRA_SERVICO - Tipo de servico ou obra conforme consta na IN/INSS/DC nº 100/2003: (Numerico de tamanho 2).
        // //                          01 - Servicos diversos sujeitos à retencao
        // //                          02 - Transporte de passageiros realizados por Pessoa Fisica
        // //                          03 - Limpeza Hospitalar
        // //                          04 - Demais Limpezas
        // //                          05 - Pavimentacao Asfaltica
        // //                          06 - Terraplanagem, Aterro Sanitario e Dragagem
        // //                          07 - Obras de Arte (pontes, viadutos, bueiros, captacao de aguas pluviais)
        // //                          08 - Drenagem
        // //                          09 - Demais servicos de construcao civil realizados com a utilizacao de equipamentos, exceto manuais
        // //                          10 - Edificacoes em geral
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tip_obra_servico");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        // //07 - CEI - Numero da matricula da obra no INSS (Numericos de tamanho 12).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cei");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        // //08 - DESC_SERV_OBRA - Descricao da obra ou servico realizado (Caracter de tamanho indefinido).
        // $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_serv_obra");
        // $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        // $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        //$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setUltimoCampo(true);

        // #######################################################################################################
        // # Registro tipo L990
        // # Encerramento do bloco L
        // #
        // # Observacoes:
        // #   Ocorrencia: 1 por arquivo
        // #######################################################################################################

         //SQL para pesquisa
        $arRegistroL990 = array( 0 => array(
            'reg' => 'L990',
            'qtd_lin_L' => $this->getNumLinhas('L')+1,
        ));

        $rsRegistroL990 = new RecordSet;
        $rsRegistroL990->preenche($arRegistroL990);

        $this->arCountRegistros['L990'] = $rsRegistroL990->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistroL990);

        //01 - REG - Texto Fixo Contendo 'L990' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - QTD_LIN_L - Quantidade total de linhas do Bloco K (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_lin_L");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    }

    /**
     * Bloco obrigatorio em todos os arquivos digitais
     *
     * Bloco 9 - CONTROLE E ENCERRAMENTO DO ARQUIVO DIGITAL
     */
    public function geraBloco9(&$obExportador)
    {
        // Iniciados antes para serem contabilizados no tipo de registro 9900
        $this->arCountRegistros['9990'] = 1;
        $this->arCountRegistros['9999'] = 1;

        #######################################################################################################
        # Registro tipo 9001
        # Abertura do bloco 9
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #######################################################################################################

        $arRegistro9001 = array( 0 => array (
            'reg' => '9001',
            'ind_mov' => 0
        ));

        $rsRegistro9001 = new RecordSet;
        $rsRegistro9001->preenche($arRegistro9001);

        $this->arCountRegistros['9001'] = $rsRegistro9001->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro9001);

        //01 - REG - Texto Fixo Contendo '9001' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - IND_MOV - Indicador de movimento: (Numerico de tamanho 1)
        //                  0 - Bloco com dados informados;
        //                  1 - Bloco sem dados informados;
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_mov");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        #######################################################################################################
        # Registro tipo 9900
        # Registro dos Blocos
        #
        # Observacoes:
        #   Ocorrencia: varios (1 para cada tipo de registro presente no arquivo)
        #######################################################################################################

        $rsRegistro9900 = $this->getRecordSetLinhas();

        $this->arCountRegistros['9900'] = $rsRegistro9900->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro9900);

        //01 - REG - Texto Fixo Contendo '9900' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - TIP_REG - Tipo de registro que sera totalizando no proximo campo (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tip_reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //03 - QTD_REG - Total de registros do tipo informado no campo anterior (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        #######################################################################################################
        # Registro tipo 9900
        # Registro dos Blocos
        #
        # Observacoes:
        #   Ocorrencia: varios (1 para cada tipo de registro presente no arquivo)
        #######################################################################################################

        $arRegistro9900 = array( 0 => array (
              'reg'     => '9900',
              'tip_reg' => '9900',
              'qtd_reg' =>   $rsRegistro9900->getNumLinhas()+1
        ));
        $rsReg9900 = new RecordSet;
        $rsReg9900->preenche($arRegistro9900);

        //$this->arCountRegistros['9900'] = $rsRegistro9900->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsReg9900);

        //01 - REG - Texto Fixo Contendo '9900' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - TIP_REG - Tipo de registro que sera totalizando no proximo campo (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tip_reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //03 - QTD_REG - Total de registros do tipo informado no campo anterior (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        #######################################################################################################
        # Registro tipo 9990
        # Encerramento do bloco 9
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #   - A quantidade total de linhas no arquivo devera levar em consideracao todos os registros entre o 9000 e o 9999, inclusive.
        #######################################################################################################

        $arRegistro9990 = array( 0 => array(
            'reg' => '9990',
            'qtd_lin_9' => $this->getNumLinhas('9')+1,
        ));

        $rsRegistro9990 = new RecordSet;
        $rsRegistro9990->preenche($arRegistro9990);

        $obExportador->roUltimoArquivo->addBloco($rsRegistro9990);

        //01 - REG - Texto Fixo Contendo '9990' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - QTD_LIN_9 - Quantidade total de linhas do Bloco 9 (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_lin_9");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

        #######################################################################################################
        # Registro tipo 9999
        # Encerramento do Arquivo Digital
        #
        # Observacoes:
        #   Ocorrencia: 1 por arquivo
        #   - A quantidade total de linhas no arquivo devera levar em consideracao todos os registros entre o 0000 e o 9999, inclusive.
        #######################################################################################################

        // $rsTotalLinhas = $this->getRecordSetLinhas();

        $inCountTotal = $this->getNumLinhas('0') +
                        //$this->getNumLinhas('I') +
                        $this->getNumLinhas('K') +
                        $this->getNumLinhas('L') +
                        $this->getNumLinhas('9');

        $arRegistro9999 = array( 0 => array(
            'reg' => '9999',
            'qtd_lin' => $inCountTotal+1,
        ));

        $rsRegistro9999 = new RecordSet;
        $rsRegistro9999->preenche($arRegistro9999);
        $this->arCountRegistros['9999'] = $rsRegistro9990->getNumLinhas();

        $obExportador->roUltimoArquivo->addBloco($rsRegistro9999);

        //01 - REG - Texto Fixo Contendo '9999' (Caracter de tamanho 4).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reg");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador("|");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        //02 - QTD_LIN_9 - Quantidade total de linhas do arquivo digital (Numerico de tamanho indefinido).
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_lin");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");

    }
}
