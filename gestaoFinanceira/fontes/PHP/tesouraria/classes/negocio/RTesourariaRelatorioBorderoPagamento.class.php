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
    * Classe de Regra do Relatório de Situação de Empenho
    * Data de Criação   : 26/01/2006

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30835 $
    $Name$
    $Author: cako $
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_FW_PDF."RRelatorio.class.php"                 );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO     ."RAdministracaoConfiguracao.class.php" );

/**
    * Classe de Regra de Negócios Transacoes Pagamento
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaRelatorioBorderoPagamento extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRConfiguracao;
/**
    * @var Integer
    * @access Private
*/
var $inCodBordero;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Array
    * @access Private
*/
var $arDadosBorderdo;
/**
    * @var Array
    * @access Private
*/
var $arDadosPagamento;
var $stCodOrdem;
/**
     * @access Public
     * @param Integer $valor
*/
function setCodBordero($valor) { $this->inCodBordero         = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio          = $valor; }
/**
     * @access Public
     * @param Array $valor
*/
function setDadosBordero($valor) { $this->arDadosBordero         = $valor; }
/**
     * @access Public
     * @param Array $valor
*/
function setDadosPagamento($valor) { $this->arDadosPagamento       = $valor; }
function setCodOrdem($valor) { $this->stCodOrdem             = $valor; }
/*
    * @access Public
    * @return Integer
*/
function getCodBordero() { return $this->inCodBordero;                   }
/*
    * @access Public
    * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade;                  }
/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                    }
/*
    * @access Public
    * @return Array
*/
function getDadosBordero() { return $this->arDadosBordero;                  }
/*
    * @access Public
    * @return Array
*/
function getDadosPagamento() { return $this->arDadosPagamento;                }
function getCodOrdem() { return $this->stCodOrdem;                      }

function RTesourariaRelatorioBorderoPagamento()
{
    $this->obRRelatorio                    = new RRelatorio;
    $this->obRConfiguracao      = new RAdministracaoConfiguracao;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet , $stOrder = "")
{
    $this->obRConfiguracao->consultarMunicipio();

    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->obROrcamentoEntidade->setExercicio( $this->arDadosBordero['stExercicio']);
    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade($this->arDadosBordero['inCodEntidade'] );
    $obRTesourariaBoletim->obROrcamentoEntidade->listar( $rsEntidade );

    $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();

    $obRContabilidadePlanoBanco->setExercicio($this->arDadosBordero['stExercicio'] );
    $obRContabilidadePlanoBanco->setCodPlano($this->arDadosBordero['inCodConta'] );
    $obRContabilidadePlanoBanco->consultar( $boTransacao );

    $arLinha0[0]['DadosBordero']   = "Dados do Borderô";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha0);
    $arRecordSet[0] = $rsNewRecord;

    $arLinha1[0]['NumBordero']  = "Número Borderô ";
    $arLinha1[0]['DataBordero'] = str_pad($this->arDadosBordero['inNumBordero'], 3,"0",STR_PAD_LEFT). " / " . $this->arDadosBordero['stExercicio'];
    $arLinha1[1]['NumBordero']  = "Data do Borderô ";
    $arLinha1[1]['DataBordero'] = date("d/m/Y");
    $arLinha1[2]['NumBordero']  = "Entidade ";
    $arLinha1[2]['DataBordero'] = $rsEntidade->getCampo("nom_cgm");
    $arLinha1[3]['NumBordero']  = "Tipo do Borderô ";
    $arLinha1[3]['DataBordero'] = "Pagamento";
    $arLinha1[4]['NumBordero']  = "Conta Pagadora ";
    $arLinha1[4]['DataBordero'] = $this->arDadosBordero['inCodConta'] . " - " . $this->arDadosBordero['stConta'];
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha1);
    $arRecordSet[1] = $rsNewRecord;

    $arLinha2[0]['DadosBoletim']   = "Dados do Boletim";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2);
    $arRecordSet[2] = $rsNewRecord;

    list ( $inCodBoletim , $stDtBoletim , $stExercicioBoletim ) = explode ( ':' , $this->arDadosBordero["inCodBoletim"] );

    $arLinha3[0]['NumBoletim']  = "Número do Boletim ";
    $arLinha3[0]['DataBoletim'] = str_pad($inCodBoletim, 3, "0", STR_PAD_LEFT) ." / ". $stExercicioBoletim;
    $arLinha3[1]['NumBoletim']  = "Data do Boletim ";
    $arLinha3[1]['DataBoletim'] = $stDtBoletim;
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha3);
    $arRecordSet[3] = $rsNewRecord;

    $arLinha4[0]['AoBanco']   = "Ao Banco ".$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getNumBanco()." / ".$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getNomBanco();
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha4);
    $arRecordSet[4] = $rsNewRecord;

    $arLinha5[0]['Agencia']     = "Agência ";
    $arLinha5[0]['NomAgencia']  =  $obRContabilidadePlanoBanco->obRMONAgencia->getNumAgencia()." / ". $obRContabilidadePlanoBanco->obRMONAgencia->getNomAgencia();
    $arLinha5[1]['Agencia']     = "Conta-Corrente ";
    $arLinha5[1]['NomAgencia']  = $obRContabilidadePlanoBanco->getContaCorrente();
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha5);
    $arRecordSet[5] = $rsNewRecord;

    $arLinha6[0]['Autorizacao']   = "AUTORIZAMOS ESTA AGÊNCIA BANCÁRIA A PAGAR O VALOR TOTAL DESTE BORDERÔ, ATRAVÉS DO DÉBITO EM NOSSA";
    $arLinha6[1]['Autorizacao']   = "CONTA-CORRENTE ACIMA INDICADA. EFETUAR OS PAGAMENTOS AOS FORNECEDORES RELACIONADOS, NAS CONTAS INDICADAS.";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    $arRecordSet[6] = $rsNewRecord;

    $arLinha7[0]['Credor']    = "Credor";
    $arLinha7[0]['CNPJ']      = "CPF / CNPJ";
    $arLinha7[0]['BancoAgCC'] = "Banco / Agência / C.C";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha7);
    $arRecordSet[7] = $rsNewRecord;

    $arLinha8[0]['Credor']    = "OP";
    $arLinha8[0]['CNPJ']      = "VALOR";
    $arLinha8[0]['BancoAgCC'] = "OBSERVAÇÕES";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha8);
    $arRecordSet[8] = $rsNewRecord;

    $inCount = 0;

    $rsPagamento = new RecordSet;

    array_multisort($this->arDadosPagamento, SORT_ASC, SORT_STRING);

    $rsPagamento->preenche($this->arDadosPagamento);

    $credor = $rsPagamento->getCampo("inCodCredor");

    $tipoTransacao = $rsPagamento->getCampo("stTipoTransacaoCredor");

    $segueCredor = 1;

    $segueTransacao = 1;

    $subTotalFornecedor = 0;

    $subTotalMovimento = 0;

    $totalGeral = 0;

    $inItensCredor = 0;

    $inItensMovimento = 0;

    $inItensGeral = 0;

    while (!$rsPagamento->eof() ) {

//          $inValor = str_replace( '.','',$rsPagamento->getCampo("inValor") );
//          $inValor = str_replace( ',','.',$inValor );

        $inValor = $rsPagamento->getCampo('inValor');

        if (strlen($rsPagamento->getCampo("stCPF/CNPJ")) == 14 ) {
            $obMascara = new Mascara;
            $obMascara->setMascara('99.999.999/9999-99');
            $obMascara->mascaraDado($rsPagamento->getCampo("stCPF/CNPJ"));
            $inscricao = $obMascara->getMascarado();
        } elseif (strlen($rsPagamento->getCampo("stCPF/CNPJ")) == 11) {
            $obMascara = new Mascara;
            $obMascara->setMascara('999.999.999-99');
            $obMascara->mascaraDado($rsPagamento->getCampo("stCPF/CNPJ"));
            $inscricao = $obMascara->getMascarado();
        } else {
            $inscricao = "";
        }

        switch ($rsPagamento->getCampo("stTipoTransacaoCredor")) {

            case "2":
                    $label = "TRANSFERÊNCIA CONTA CORRENTE";
                break;

            case "3":
                    $label = "TRANSFERÊNCIA POUPANÇA";
                break;

            case "4":
                    $label = "DOC";
                break;

            case "5":
                    $label = "TED";
                break;

        }

        if ($tipoTransacao == $rsPagamento->getCampo("stTipoTransacaoCredor")) {

            if ($credor == $rsPagamento->getCampo('inCodCredor')) {

                if ($segueTransacao == 1) {

                    $arLinha9[$inCount]['A']   = $label;
                    $arLinha9[$inCount]['B']   = "";
                    $arLinha9[$inCount]['C']   = "";
                    $arLinha9[$inCount]['D']   = "";

                    $segueTransacao = 0;

                    $inCount++;
                }

                if ($segueCredor == 1) {

                    $arLinha9[$inCount]['B']   = "";
                    $arLinha9[$inCount]['C']   = $inscricao;
                    $arLinha9[$inCount]['D']   = $rsPagamento->getCampo("inNumBancoCredor")." / ".$rsPagamento->getCampo("inNumAgenciaCredor")." / ".$rsPagamento->getCampo("stNumeroContaCredor");

                    $stCredor      = str_replace( chr(10) , "", $rsPagamento->getCampo("stCredor") );
                    $stCredor      = wordwrap( $stCredor , 35, chr(13) );
                    $arCredor      = explode( chr(13), $stCredor );
                    foreach ($arCredor as $stCredor) {
                        $arLinha9[$inCount]['A']   = $stCredor;
                        $inCount++;
                    }

                    $segueCredor = 0;
                }

                $arLinha9[$inCount]['A']   = $rsPagamento->getCampo("stOrdemPagamento");
                $arLinha9[$inCount]['B']   = number_format($inValor,2,',','.');
                $arLinha9[$inCount]['D']   = "NF/Docum.: " . str_pad($rsPagamento->getCampo("stNrNFDocumento"), 36 - strlen($rsPagamento->getCampo("stNrNFDocumento")), " ", STR_PAD_RIGHT);
                $stObservacao = "Obs.: " . $rsPagamento->getCampo("stObservacao");
                $stObservacao = str_replace( chr(10) , "", $stObservacao );
                $stObservacao = wordwrap( $stObservacao, 28, chr(13) );
                $arObservacao = explode( chr(13), $stObservacao );
                foreach ($arObservacao as $stObservacao) {
                    $arLinha9[$inCount]['C'] .= $stObservacao;
                    $inCount++;
                }

                $subTotalFornecedor += $inValor;

                $subTotalMovimento += $inValor;

                $totalGeral += $inValor;

                $inCount ++;

                $inItensCredor++;

                $inItensMovimento++;

                $inItensGeral ++;

            } else {

                $arLinha9[$inCount]['A']   = "Total do Fornecedor";
                $arLinha9[$inCount]['B']   = "";
                $arLinha9[$inCount]['C']   = "Itens: " . str_pad(str_pad($inItensCredor, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
                $arLinha9[$inCount]['D']   = number_format($subTotalFornecedor,2,',','.');

                $subTotalFornecedor = 0;

                $inItensCredor = 0;

                $arLinha9[$inCount+1]['A']   = "";
                $arLinha9[$inCount+1]['B']   = "";
                $arLinha9[$inCount+1]['C']   = "";
                $arLinha9[$inCount+1]['D']   = "";

                $arLinha9[$inCount+2]['B']   = "";
                $arLinha9[$inCount+2]['C']   = $inscricao;
                $arLinha9[$inCount+2]['D']   = $rsPagamento->getCampo("inNumBancoCredor")." / ".$rsPagamento->getCampo("inNumAgenciaCredor")." / ".$rsPagamento->getCampo("stNumeroContaCredor");

                $stCredor      = str_replace( chr(10) , "", $rsPagamento->getCampo("stCredor") );
                $stCredor      = wordwrap( $stCredor , 35, chr(13) );
                $arCredor      = explode( chr(13), $stCredor );
                foreach ($arCredor as $stCredor) {
                    $arLinha9[$inCount+2]['A']   = $stCredor;
                    $inCount++;
                }

                $arLinha9[$inCount+2]['A']   = $rsPagamento->getCampo("stOrdemPagamento");
                $arLinha9[$inCount+2]['B']   = number_format($inValor,2,',','.');
                $arLinha9[$inCount+2]['C']   = "Obs.: " . $rsPagamento->getCampo("stObservacao");
                $arLinha9[$inCount+2]['D']   = "NF/Docum.: " . str_pad($rsPagamento->getCampo("stNrNFDocumento"), 36 - strlen($rsPagamento->getCampo("stNrNFDocumento")), " ", STR_PAD_RIGHT);

                $inItensCredor ++;

                $inItensMovimento ++;

                $inItensGeral ++;

                $subTotalFornecedor += $inValor;

                $subTotalMovimento += $inValor;

                $totalGeral += $inValor;

                $inCount += 3;
            }

        } else {

            $arLinha9[$inCount]['A']   = "Total do Fornecedor";
            $arLinha9[$inCount]['B']   = "";
            $arLinha9[$inCount]['C']   = "Itens: " . str_pad(str_pad($inItensCredor, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
            $arLinha9[$inCount]['D']   = number_format($subTotalFornecedor,2,',','.');

            $subTotalFornecedor = 0;

            $inItensCredor = 0;

            $arLinha9[$inCount+1]['A']   = "";
            $arLinha9[$inCount+1]['B']   = "";
            $arLinha9[$inCount+1]['C']   = "";
            $arLinha9[$inCount+1]['D']   = "";

            $arLinha9[$inCount+2]['A']   = "Total do Movimento";
            $arLinha9[$inCount+2]['B']   = "";
            $arLinha9[$inCount+2]['C']   = "Itens: " . str_pad(str_pad($inItensMovimento, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
            $arLinha9[$inCount+2]['D']   = number_format($subTotalMovimento,2,',','.');

            $inItensMovimento = 0;

            $subTotalMovimento = 0;

            $arLinha9[$inCount+3]['A']   = "";
            $arLinha9[$inCount+3]['B']   = "";
            $arLinha9[$inCount+3]['C']   = "";
            $arLinha9[$inCount+3]['D']   = "";

            $arLinha9[$inCount+4]['A']   = $label;
            $arLinha9[$inCount+4]['B']   = "";
            $arLinha9[$inCount+4]['C']   = "";
            $arLinha9[$inCount+4]['D']   = "";

            $segueTransacao = 0;

            $arLinha9[$inCount+5]['B']   = "";
            $arLinha9[$inCount+5]['C']   = $inscricao;
            $arLinha9[$inCount+5]['D']   = $rsPagamento->getCampo("inNumBancoCredor")." / ".$rsPagamento->getCampo("inNumAgenciaCredor")." / ".$rsPagamento->getCampo("stNumeroContaCredor");

            $stCredor      = str_replace( chr(10) , "", $rsPagamento->getCampo("stCredor") );
            $stCredor      = wordwrap( $stCredor , 35, chr(13) );
            $arCredor      = explode( chr(13), $stCredor );
            foreach ($arCredor as $stCredor) {
                $arLinha9[$inCount+5]['A']   = $stCredor;
                $inCount++;
            }

            $segueCredor = 0;

            $arLinha9[$inCount+5]['A']   = $rsPagamento->getCampo("stOrdemPagamento");
            $arLinha9[$inCount+5]['B']   = number_format($inValor,2,',','.');
            $arLinha9[$inCount+5]['C']   = "Obs.: " . $rsPagamento->getCampo("stObservacao");
            $arLinha9[$inCount+5]['D']   = "NF/Docum.: " . str_pad($rsPagamento->getCampo("stNrNFDocumento"), 36 - strlen($rsPagamento->getCampo("stNrNFDocumento")), " ", STR_PAD_RIGHT);

            $inItensCredor ++;

            $inItensMovimento ++;

            $inItensGeral ++;

            $subTotalFornecedor += $inValor;

            $subTotalMovimento += $inValor;

            $totalGeral += $inValor;

            $inCount += 6;

        }

        $credor = $rsPagamento->getCampo['inCodCredor'];

        $tipoTransacao = $rsPagamento->getCampo("stTipoTransacaoCredor");

        $rsPagamento->proximo();
    }

    $arLinha9[$inCount]['A'] = "Total do Fornecedor";
    $arLinha9[$inCount]['B'] = "";
    $arLinha9[$inCount]['C'] = "Itens: " . str_pad(str_pad($inItensCredor, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
    $arLinha9[$inCount]['D'] = number_format($subTotalFornecedor,2,',','.');

    $arLinha9[$inCount+1]['A'] = "";
    $arLinha9[$inCount+1]['B'] = "";
    $arLinha9[$inCount+1]['C'] = "";
    $arLinha9[$inCount+1]['D'] = "";

    $arLinha9[$inCount+2]['A'] = "Total do Movimento";
    $arLinha9[$inCount+2]['B'] = "";
    $arLinha9[$inCount+2]['C'] = "Itens: " . str_pad(str_pad($inItensMovimento, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
    $arLinha9[$inCount+2]['D'] = number_format($subTotalMovimento,2,',','.');

    $arLinha9[$inCount+3]['A'] = "";
    $arLinha9[$inCount+3]['B'] = "";
    $arLinha9[$inCount+3]['C'] = "";
    $arLinha9[$inCount+3]['D'] = "";

    $arLinha9[$inCount+4]['A'] = "TOTAL DESTE BORDERÔ";
    $arLinha9[$inCount+4]['B'] = "";
    $arLinha9[$inCount+4]['C'] = "Itens: " . str_pad(str_pad($inItensGeral, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
    $arLinha9[$inCount+4]['D'] = number_format($totalGeral,2,',','.');

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha9);
    $arRecordSet[9] = $rsNewRecord;

    $arLinha10[0]['Autorizo']   = "Autorizo.";
    $arLinha10[0]['Cidade']     = $this->obRConfiguracao->getNomMunicipio() .", ". SistemaLegado::dataExtenso(date("Y-m-d"));
    $arLinha10[1]['Autorizo']   = "";
    $arLinha10[1]['Cidade']     = "";
    $arLinha10[2]['Autorizo']   = "";
    $arLinha10[2]['Cidade']     = "";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha10);
    $arRecordSet[10] = $rsNewRecord;

    $inCount = 0;

    $stAssinantes = "";

    for ($x=1; $x<=3; $x++) {

        if ($this->arDadosBordero["inNumAssinante_".$x]) {

            $stAssinantes .= $x . "#";

            $inCount ++;
        }
    }

    if ($stAssinantes) {

        $stAssinantes = substr($stAssinantes,0,strlen($stAssinantes)-1);

        $stAssinantes = explode("#",$stAssinantes);

        if ($inCount == 1) {

            $arLinha11[0]["Assinante_1"] = "";
            $arLinha11[1]["Assinante_1"] = "";
            $arLinha11[2]["Assinante_1"] = "";

            $arLinha11[0]["Assinante_2"] = "";
            $arLinha11[1]["Assinante_2"] = "";
            $arLinha11[2]["Assinante_2"] = "";

            $arLinha11[0]["Assinante_3"] = $this->arDadosBordero["stNomAssinante_".$stAssinantes[0]];
            $arLinha11[1]["Assinante_3"] = $this->arDadosBordero["stCargo_".$stAssinantes[0]];
            $arLinha11[2]["Assinante_3"] = "Matr. " . $this->arDadosBordero["inNumMatricula_".$stAssinantes[0]];

        }
        if ($inCount == 2) {

            $arLinha11[0]["Assinante_1"] = $this->arDadosBordero["stNomAssinante_".$stAssinantes[1]];
            $arLinha11[1]["Assinante_1"] = $this->arDadosBordero["stCargo_".$stAssinantes[1]];
            $arLinha11[2]["Assinante_1"] = "Matr. " . $this->arDadosBordero["inNumMatricula_".$stAssinantes[1]];

            $arLinha11[0]["Assinante_2"] = "";
            $arLinha11[1]["Assinante_2"] = "";
            $arLinha11[2]["Assinante_2"] = "";

            $arLinha11[0]["Assinante_3"] = $this->arDadosBordero["stNomAssinante_".$stAssinantes[0]];
            $arLinha11[1]["Assinante_3"] = $this->arDadosBordero["stCargo_".$stAssinantes[0]];
            $arLinha11[2]["Assinante_3"] = "Matr. " . $this->arDadosBordero["inNumMatricula_".$stAssinantes[0]];

        }
        if ($inCount == 3) {

            for ($x=1; $x<=3; $x++) {

                $arLinha11[0]["Assinante_".$x] =  $this->arDadosBordero["stNomAssinante_".$x];
                $arLinha11[1]["Assinante_".$x] =  $this->arDadosBordero["stCargo_".$x];
                $arLinha11[2]["Assinante_".$x] =  "Matr. " . $this->arDadosBordero["inNumMatricula_".$x];

            }
        }

    } else {
        $arLinha11 = array();
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha11);
    $arRecordSet[11] = $rsNewRecord;

    $arLinha12[0]['Simulacao']   = "";
    $arLinha12[1]['Simulacao']   = "SIMULAÇÃO DO BORDERÔ";
    $arLinha12[2]['Simulacao']   = "";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha12);
    $arRecordSet[12] = $rsNewRecord;

    return $obErro;

}

function geraRecordSetBorderoPagamento(&$arRecordSet , $stOrder = "")
{
    $this->obRConfiguracao->consultarMunicipio();

    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->addBordero();
    $obRTesourariaBoletim->roUltimoBordero->setCodBordero($this->getCodBordero());
    $obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($this->getCodEntidade());
    $obRTesourariaBoletim->roUltimoBordero->setExercicio($this->getExercicio());
    $stListaOP = $obRTesourariaBoletim->roUltimoBordero->getListaOP();
    $obRTesourariaBoletim->roUltimoBordero->setCodOrdem( $stListaOP );

    $obRTesourariaBoletim->roUltimoBordero->consultar( $rsBordero );
    $obRTesourariaBoletim->roUltimoBordero->addTransacaoPagamento();
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->setCodBordero($this->getCodBordero());
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->obROrcamentoEntidade->setCodigoEntidade($this->getCodEntidade());
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->setExercicio($this->getExercicio());
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->setCodOrdem ($stListaOP );
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->listar( $rsPagamento );

    $obRTesourariaBoletim->roUltimoBordero->addAssinatura();
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio( $rsBordero->getCampo("exercicio_bordero") );
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setEntidades( $rsBordero->getCampo("cod_entidade") );
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->listar( $rsAssinatura );

    $arLinha0[0]['DadosBordero']   = "Dados do Borderô";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha0);
    $arRecordSet[0] = $rsNewRecord;

    $arLinha1[0]['NumBordero']  = "Número Borderô ";
    $arLinha1[0]['DataBordero'] = str_pad($rsBordero->getCampo("cod_bordero"), 3, "0",STR_PAD_LEFT). " / " . $rsBordero->getCampo("exercicio_bordero");
    $arLinha1[1]['NumBordero']  = "Data do Borderô ";
    $arLinha1[1]['DataBordero'] = $rsBordero->getCampo("dt_bordero");
    $arLinha1[2]['NumBordero']  = "Entidade ";
    $arLinha1[2]['DataBordero'] = $rsBordero->getCampo("nom_cgm");
    $arLinha1[3]['NumBordero']  = "Tipo do Borderô ";
    $arLinha1[3]['DataBordero'] = "Pagamento";
    $arLinha1[4]['NumBordero']  = "Conta Pagadora ";
    $arLinha1[4]['DataBordero'] = $rsBordero->getCampo("cod_plano") . " - " . $rsBordero->getCampo("nom_conta");
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha1);
    $arRecordSet[1] = $rsNewRecord;

    $arLinha2[0]['DadosBoletim']   = "Dados do Boletim";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2);
    $arRecordSet[2] = $rsNewRecord;

    $arLinha3[0]['NumBoletim']  = "Número do Boletim ";
    $arLinha3[0]['DataBoletim'] = str_pad($rsBordero->getCampo("cod_boletim"), 3, "0", STR_PAD_LEFT) ." / ". $rsBordero->getCampo("exercicio_boletim");
    $arLinha3[1]['NumBoletim']  = "Data do Boletim ";
    $arLinha3[1]['DataBoletim'] = $rsBordero->getCampo("dt_boletim");
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha3);
    $arRecordSet[3] = $rsNewRecord;

    $arLinha4[0]['AoBanco']   = "Ao Banco ".$rsBordero->getCampo("num_banco")." / ".$rsBordero->getCampo("nom_banco");
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha4);
    $arRecordSet[4] = $rsNewRecord;

    $arLinha5[0]['Agencia']     = "Agência ";
    $arLinha5[0]['NomAgencia']  = $rsBordero->getCampo("num_agencia") ." / ". $rsBordero->getCampo("nom_agencia");
    $arLinha5[1]['Agencia']     = "Conta-Corrente ";
    $arLinha5[1]['NomAgencia']  = $rsBordero->getCampo("conta_corrente");
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha5);
    $arRecordSet[5] = $rsNewRecord;

    $arLinha6[0]['Autorizacao']   = "AUTORIZAMOS ESTA AGÊNCIA BANCÁRIA A PAGAR O VALOR TOTAL DESTE BORDERÔ, ATRAVÉS DO DÉBITO EM NOSSA";
    $arLinha6[1]['Autorizacao']   = "CONTA-CORRENTE ACIMA INDICADA. EFETUAR OS PAGAMENTOS AOS FORNECEDORES RELACIONADOS, NAS CONTAS INDICADAS.";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    $arRecordSet[6] = $rsNewRecord;

    $arLinha7[0]['Credor']    = "Credor";
    $arLinha7[0]['CNPJ']      = "CPF / CNPJ";
    $arLinha7[0]['BancoAgCC'] = "Banco / Agência / C.C";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha7);
    $arRecordSet[7] = $rsNewRecord;

    $arLinha8[0]['Credor']    = "OP";
    $arLinha8[0]['CNPJ']      = "VALOR";
    $arLinha8[0]['BancoAgCC'] = "OBSERVAÇÕES";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha8);
    $arRecordSet[8] = $rsNewRecord;

    $inCount = 0;

    $credor = $rsPagamento->getCampo("num_cgm_pagamento");

    $tipoTransacao = $rsPagamento->getCampo("cod_tipo");

    $segueCredor = 1;

    $segueTransacao = 1;

    $subTotalFornecedor = 0;

    $subTotalMovimento = 0;

    $totalGeral = 0;

    $inItensCredor = 0;

    $inItensMovimento = 0;

    $inItensGeral = 0;

    while (!$rsPagamento->eof() ) {

        if (strlen($rsPagamento->getCampo("inscricao")) == 14 ) {
            $obMascara = new Mascara;
            $obMascara->setMascara('99.999.999/9999-99');
            $obMascara->mascaraDado($rsPagamento->getCampo("inscricao"));
            $inscricao = $obMascara->getMascarado();
        } elseif (strlen($rsPagamento->getCampo("inscricao")) == 11) {
            $obMascara = new Mascara;
            $obMascara->setMascara('999.999.999-99');
            $obMascara->mascaraDado($rsPagamento->getCampo("inscricao"));
            $inscricao = $obMascara->getMascarado();
        } else {
            $inscricao = "";
        }

        switch ($rsPagamento->getCampo("cod_tipo")) {

            case 2:
                    $label = "TRANSFERÊNCIA CONTA CORRENTE";
                break;

            case 3:
                    $label = "TRANSFERÊNCIA POUPANÇA";
                break;

            case 4:
                    $label = "DOC";
                break;

            case 5:
                    $label = "TED";
                break;

        }

        if ($tipoTransacao == $rsPagamento->getCampo("cod_tipo")) {

            if ($credor == $rsPagamento->getCampo('num_cgm_pagamento')) {

                if ($segueTransacao == 1) {

                    $arLinha9[$inCount]['A']   = $label;
                    $arLinha9[$inCount]['B']   = "";
                    $arLinha9[$inCount]['C']   = "";
                    $arLinha9[$inCount]['D']   = "";

                    $segueTransacao = 0;

                    $inCount++;
                }

                if ($segueCredor == 1) {

                    $arLinha9[$inCount]['B']   = "";
                    $arLinha9[$inCount]['C']   = $inscricao;
                    $arLinha9[$inCount]['D']   = $rsPagamento->getCampo("num_banco_pagamento")." / ".$rsPagamento->getCampo("num_agencia_pagamento")." / ".$rsPagamento->getCampo("conta_corrente_pagamento");

                    $stCredor = $rsPagamento->getCampo("num_cgm_pagamento")." - ".trim( $rsPagamento->getCampo("nom_cgm_pagamento") );

                    $stCredor      = str_replace( chr(10) , "", $stCredor );
                    $stCredor      = wordwrap( $stCredor , 35, chr(13) );
                    $arCredor      = explode( chr(13), $stCredor );
                    foreach ($arCredor as $stCredor) {
                        $arLinha9[$inCount]['A']   = $stCredor;
                        $inCount++;
                    }

                    $segueCredor = 0;
                }

                $arLinha9[$inCount]['A']   = str_pad($rsPagamento->getCampo("cod_ordem"), 6, "0", STR_PAD_LEFT)." / ".$rsPagamento->getCampo("exercicio_pagamento");
                $arLinha9[$inCount]['B']   = number_format($rsPagamento->getCampo("vl_pagamento"),2,',','.');
                $arLinha9[$inCount]['D']   = "NF/Docum.: " . str_pad($rsPagamento->getCampo("documento"), 36 - strlen($rsPagamento->getCampo("documento")), " ", STR_PAD_RIGHT);
                $stObservacao = "Obs.: " . $rsPagamento->getCampo("descricao");
                $stObservacao = str_replace( chr(10) , "", $stObservacao );
                $stObservacao = wordwrap( $stObservacao, 28, chr(13) );
                $arObservacao = explode( chr(13), $stObservacao );
                foreach ($arObservacao as $stObservacao) {
                    $arLinha9[$inCount]['C'] .= $stObservacao;
                    $inCount++;
                }

                $subTotalFornecedor += $rsPagamento->getCampo("vl_pagamento");

                $subTotalMovimento += $rsPagamento->getCampo("vl_pagamento");

                $totalGeral += $rsPagamento->getCampo("vl_pagamento");

                $inCount ++;

                $inItensCredor++;

                $inItensMovimento++;

                $inItensGeral ++;

            } else {

                $arLinha9[$inCount]['A']   = "Total do Fornecedor";
                $arLinha9[$inCount]['B']   = "";
                $arLinha9[$inCount]['C']   = "Itens: " . str_pad(str_pad($inItensCredor, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
                $arLinha9[$inCount]['D']   = number_format($subTotalFornecedor,2,',','.');

                $subTotalFornecedor = 0;

                $inItensCredor = 0;

                $arLinha9[$inCount+1]['A']   = "";
                $arLinha9[$inCount+1]['B']   = "";
                $arLinha9[$inCount+1]['C']   = "";
                $arLinha9[$inCount+1]['D']   = "";

                $arLinha9[$inCount+2]['B']   = "";
                $arLinha9[$inCount+2]['C']   = $inscricao;
                $arLinha9[$inCount+2]['D']   = $rsPagamento->getCampo("num_banco_pagamento")." / ".$rsPagamento->getCampo("num_agencia_pagamento")." / ".$rsPagamento->getCampo("conta_corrente_pagamento");

                $stCredor = $rsPagamento->getCampo("num_cgm_pagamento")." - ".trim( $rsPagamento->getCampo("nom_cgm_pagamento") );

                $stCredor      = str_replace( chr(10) , "", $stCredor );
                $stCredor      = wordwrap( $stCredor , 35, chr(13) );
                $arCredor      = explode( chr(13), $stCredor );
                foreach ($arCredor as $stCredor) {
                    $arLinha9[$inCount+2]['A']   = $stCredor;
                    $inCount++;
                }

                $arLinha9[$inCount+2]['A']   = str_pad($rsPagamento->getCampo("cod_ordem"), 6, "0", STR_PAD_LEFT)." / ".$rsPagamento->getCampo("exercicio_pagamento");
                $arLinha9[$inCount+2]['B']   = number_format($rsPagamento->getCampo("vl_pagamento"),2,',','.');
                $arLinha9[$inCount+2]['C']   = "Obs.: " . $rsPagamento->getCampo("descricao");
                $arLinha9[$inCount+2]['D']   = "NF/Docum.: " . str_pad($rsPagamento->getCampo("documento"), 36 - strlen($rsPagamento->getCampo("documento")), " ", STR_PAD_RIGHT);

                $inItensCredor ++;

                $inItensMovimento ++;

                $inItensGeral ++;

                $subTotalFornecedor += $rsPagamento->getCampo("vl_pagamento");

                $subTotalMovimento += $rsPagamento->getCampo("vl_pagamento");

                $totalGeral += $rsPagamento->getCampo("vl_pagamento");

                $inCount += 3;
            }

        } else {

            $arLinha9[$inCount]['A']   = "Total do Fornecedor";
            $arLinha9[$inCount]['B']   = "";
            $arLinha9[$inCount]['C']   = "Itens: " . str_pad(str_pad($inItensCredor, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
            $arLinha9[$inCount]['D']   = number_format($subTotalFornecedor,2,',','.');

            $subTotalFornecedor = 0;

            $inItensCredor = 0;

            $arLinha9[$inCount+1]['A']   = "";
            $arLinha9[$inCount+1]['B']   = "";
            $arLinha9[$inCount+1]['C']   = "";
            $arLinha9[$inCount+1]['D']   = "";

            $arLinha9[$inCount+2]['A']   = "Total do Movimento";
            $arLinha9[$inCount+2]['B']   = "";
            $arLinha9[$inCount+2]['C']   = "Itens: " . str_pad(str_pad($inItensMovimento, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
            $arLinha9[$inCount+2]['D']   = number_format($subTotalMovimento,2,',','.');

            $inItensMovimento = 0;

            $subTotalMovimento = 0;

            $arLinha9[$inCount+3]['A']   = "";
            $arLinha9[$inCount+3]['B']   = "";
            $arLinha9[$inCount+3]['C']   = "";
            $arLinha9[$inCount+3]['D']   = "";

            $arLinha9[$inCount+4]['A']   = $label;
            $arLinha9[$inCount+4]['B']   = "";
            $arLinha9[$inCount+4]['C']   = "";
            $arLinha9[$inCount+4]['D']   = "";

            $segueTransacao = 0;

            $arLinha9[$inCount+5]['B']   = "";
            $arLinha9[$inCount+5]['C']   = $inscricao;
            $arLinha9[$inCount+5]['D']   = $rsPagamento->getCampo("num_banco_pagamento")." / ".$rsPagamento->getCampo("num_agencia_pagamento")." / ".$rsPagamento->getCampo("conta_corrente_pagamento");

            $stCredor = $rsPagamento->getCampo("num_cgm_pagamento")." - ".trim( $rsPagamento->getCampo("nom_cgm_pagamento") );

            $stCredor      = str_replace( chr(10) , "", $stCredor );
            $stCredor      = wordwrap( $stCredor , 35, chr(13) );
            $arCredor      = explode( chr(13), $stCredor );
            foreach ($arCredor as $stCredor) {
                $arLinha9[$inCount+5]['A']   = $stCredor;
                $inCount++;
            }

            $segueCredor = 0;

            $arLinha9[$inCount+5]['A']   = str_pad($rsPagamento->getCampo("cod_ordem"), 6, "0", STR_PAD_LEFT)." / ".$rsPagamento->getCampo("exercicio_pagamento");
            $arLinha9[$inCount+5]['B']   = number_format($rsPagamento->getCampo("vl_pagamento"),2,',','.');
            $arLinha9[$inCount+5]['C']   = "Obs.: " . $rsPagamento->getCampo("descricao");
            $arLinha9[$inCount+5]['D']   = "NF/Docum.: " . str_pad($rsPagamento->getCampo("documento"), 36 - strlen($rsPagamento->getCampo("documento")), " ", STR_PAD_RIGHT);

            $inItensCredor ++;

            $inItensMovimento ++;

            $inItensGeral ++;

            $subTotalFornecedor += $rsPagamento->getCampo("vl_pagamento");

            $subTotalMovimento += $rsPagamento->getCampo("vl_pagamento");

            $totalGeral += $rsPagamento->getCampo("vl_pagamento");

            $inCount += 6;

        }

        $credor = $rsPagamento->getCampo['num_cgm_pagamento'];

        $tipoTransacao = $rsPagamento->getCampo("cod_tipo");

        $rsPagamento->proximo();
    }

    $arLinha9[$inCount]['A'] = "Total do Fornecedor";
    $arLinha9[$inCount]['B'] = "";
    $arLinha9[$inCount]['C'] = "Itens: " . str_pad(str_pad($inItensCredor, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
    $arLinha9[$inCount]['D'] = number_format($subTotalFornecedor,2,',','.');

    $arLinha9[$inCount+1]['A'] = "";
    $arLinha9[$inCount+1]['B'] = "";
    $arLinha9[$inCount+1]['C'] = "";
    $arLinha9[$inCount+1]['D'] = "";

    $arLinha9[$inCount+2]['A'] = "Total do Movimento";
    $arLinha9[$inCount+2]['B'] = "";
    $arLinha9[$inCount+2]['C'] = "Itens: " . str_pad(str_pad($inItensMovimento, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
    $arLinha9[$inCount+2]['D'] = number_format($subTotalMovimento,2,',','.');

    $arLinha9[$inCount+3]['A'] = "";
    $arLinha9[$inCount+3]['B'] = "";
    $arLinha9[$inCount+3]['C'] = "";
    $arLinha9[$inCount+3]['D'] = "";

    $arLinha9[$inCount+4]['A'] = "TOTAL DESTE BORDERÔ";
    $arLinha9[$inCount+4]['B'] = "";
    $arLinha9[$inCount+4]['C'] = "Itens: " . str_pad(str_pad($inItensGeral, 3, "0", STR_PAD_LEFT), 20, " ", STR_PAD_LEFT);
    $arLinha9[$inCount+4]['D'] = number_format($totalGeral,2,',','.');

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha9);
    $arRecordSet[9] = $rsNewRecord;

    $arLinha10[0]['Autorizo']   = "Autorizo.";
    $arLinha10[0]['Cidade']     = $this->obRConfiguracao->getNomMunicipio() .", ". SistemaLegado::dataExtenso(date("Y-m-d"));
    $arLinha10[1]['Autorizo']   = "";
    $arLinha10[1]['Cidade']     = "";
    $arLinha10[2]['Autorizo']   = "";
    $arLinha10[2]['Cidade']     = "";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha10);
    $arRecordSet[10] = $rsNewRecord;

    if ($rsAssinatura->getNumLinhas() == 1) {

        $arLinha11[0]["Assinante_1"] = "";
        $arLinha11[1]["Assinante_1"] = "";
        $arLinha11[2]["Assinante_1"] = "";

        $arLinha11[0]["Assinante_2"] = "";
        $arLinha11[1]["Assinante_2"] = "";
        $arLinha11[2]["Assinante_2"] = "";

        $arLinha11[0]["Assinante_3"] = $rsAssinatura->getCampo("nom_cgm");
        $arLinha11[1]["Assinante_3"] = $rsAssinatura->getCampo("cargo");
        $arLinha11[2]["Assinante_3"] = "Matr. " . $rsAssinatura->getCampo("num_matricula");

    } elseif ($rsAssinatura->getNumLinhas() == 2) {

        $arLinha11[0]["Assinante_1"] = $rsAssinatura->getCampo("nom_cgm");
        $arLinha11[1]["Assinante_1"] = $rsAssinatura->getCampo("cargo");
        $arLinha11[2]["Assinante_1"] = "Matr. " . $rsAssinatura->getCampo("num_matricula");

        $arLinha11[0]["Assinante_2"] = "";
        $arLinha11[1]["Assinante_2"] = "";
        $arLinha11[2]["Assinante_2"] = "";

        $rsAssinatura->proximo();

        $arLinha11[0]["Assinante_3"] = $rsAssinatura->getCampo("nom_cgm");
        $arLinha11[1]["Assinante_3"] = $rsAssinatura->getCampo("cargo");
        $arLinha11[2]["Assinante_3"] = "Matr. " . $rsAssinatura->getCampo("num_matricula");

    } elseif ($rsAssinatura->getNumLinhas() == 3) {

        $x = 1;

        while (!$rsAssinatura->eof() ) {

            $arLinha11[0]["Assinante_".$x] = $rsAssinatura->getCampo("nom_cgm");
            $arLinha11[1]["Assinante_".$x] = $rsAssinatura->getCampo("cargo");
            $arLinha11[2]["Assinante_".$x] = "Matr. " . $rsAssinatura->getCampo("num_matricula");
            $rsAssinatura->proximo();
            $x++;
        }
    } else {
        $arLinha11 = array();
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha11);
    $arRecordSet[11] = $rsNewRecord;

    return $obErro;

}

function geraRecordSetBorderoPagamento2015(&$arRecordSet)
{
    $this->obRConfiguracao->consultarMunicipio();

    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->obROrcamentoEntidade->setExercicio( $this->arDadosBordero['stExercicio']);
    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade($this->arDadosBordero['inCodEntidade'] );
    $obRTesourariaBoletim->obROrcamentoEntidade->listar( $rsEntidade );

    $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();

    $obRContabilidadePlanoBanco->setExercicio($this->arDadosBordero['stExercicio'] );
    $obRContabilidadePlanoBanco->setCodPlano($this->arDadosBordero['inCodConta'] );
    $obRContabilidadePlanoBanco->consultar( $boTransacao );

    switch ($this->arDadosPagamento[0]['stTipoTransacaoCredor']) {
        case "2":
                $stTipoTransacao = "Transferência Conta Corrente";
            break;

        case "3":
                $stTipoTransacao = "Transferência Poupança";
            break;

        case "4":
                $stTipoTransacao = "DOC";
            break;

        case "5":
                $stTipoTransacao = "TED";
            break;
    }

    $arDadosPagamento['dados_bordero'][0]['numero_bordero'] = str_pad($this->arDadosBordero['inNumBordero'], 3,"0",STR_PAD_LEFT). " / " . $this->arDadosBordero['stExercicio'];
    $arDadosPagamento['dados_bordero'][0]['data_bordero']   = $this->arDadosBordero['stDtBoletim'];
    $arDadosPagamento['dados_bordero'][0]['entidade']       = $rsEntidade->getCampo("nom_cgm");
    $arDadosPagamento['dados_bordero'][0]['tipo_bordero']   = $stTipoTransacao;
    $arDadosPagamento['dados_bordero'][0]['conta_pagadora'] = $this->arDadosBordero['inCodConta'] . " - " . $this->arDadosBordero['stConta'];

    list ( $inCodBoletim , $stDtBoletim , $stExercicioBoletim ) = explode ( ':' , $this->arDadosBordero["inCodBoletim"] );
    $arDadosPagamento['dados_boletim'][0]['numero_boletim'] = str_pad($inCodBoletim, 3, "0", STR_PAD_LEFT) ." / ". $stExercicioBoletim;
    $arDadosPagamento['dados_boletim'][0]['data_boletim']   = $stDtBoletim;

    $arDadosPagamento['dados_banco_titulo'][0]['dados_banco'] = "Ao Banco ".$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getNumBanco()." / ".$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getNomBanco();
    $arDadosPagamento['dados_banco'][0]['agencia']            = $obRContabilidadePlanoBanco->obRMONAgencia->getNumAgencia()." / ". $obRContabilidadePlanoBanco->obRMONAgencia->getNomAgencia();
    $arDadosPagamento['dados_banco'][0]['conta_corrente']     = $obRContabilidadePlanoBanco->getContaCorrente();

    $arDadosPagamento['autorizacao'] = "Autorizamos esta agência bancária a DEBITAR o valor total deste Borderô em nossa conta corrente acima especificada, 
                                    e CREDITAR as respectivas contas bancárias dos credores abaixo relacionados.";

    
    include_once( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );
    $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;

    //Função para ordenar o array pelo cod_credor para facilitar o somatorio dos pagamentos
    usort($this->arDadosPagamento, function($a, $b) {
            return $a['inCodCredor'] - $b['inCodCredor'];
    });

    foreach ($this->arDadosPagamento as $chave => $valor) {

        if (strlen($valor["stCPF/CNPJ"]) == 14 ) {
            $obMascara = new Mascara;
            $obMascara->setMascara('99.999.999/9999-99');
            $obMascara->mascaraDado($valor["stCPF/CNPJ"]);
            $inscricao = $obMascara->getMascarado();
        } elseif (strlen($valor["stCPF/CNPJ"]) == 11) {
            $obMascara = new Mascara;
            $obMascara->setMascara('999.999.999-99');
            $obMascara->mascaraDado($valor["stCPF/CNPJ"]);
            $inscricao = $obMascara->getMascarado();
        } else {
            $inscricao = "";
        }

        $obREmpenhoOrdemPagamento->setCodigoOrdem                           ( $valor['inNumOrdemPagamentoCredor'] );
        $obREmpenhoOrdemPagamento->setExercicio                             ( $valor['stExercicioOrdem'] );
        $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade  ( $valor['inCodigoEntidade'] );
        $obREmpenhoOrdemPagamento->listarDadosPagamentoBordero              ( $rsLista , $boTransacao );

        $inValorOP = bcsub($rsLista->getCampo("valor_pagamento"),$rsLista->getCampo('vl_anulado'),2);

        /* Retenções */
        if ($obREmpenhoOrdemPagamento->getRetencao()) {
            foreach ( $obREmpenhoOrdemPagamento->getRetencoes() as $arRetencoes ) {
                $nuVlRetencoes = bcadd($arRetencoes['vl_retencao'],$nuVlRetencoes,2);
            }
            // Valor liquido da OP
            $inValorLiquidoOP = bcsub($inValorOP,$nuVlRetencoes,2);
        } else {
            $nuVlRetencoes = 0.00;
            // Valor liquido da OP
            $inValorLiquidoOP = $inValorOP;
        }

        //verificando se o mesmo credor ja foi inserido no array para fazer o somatorio por credor
        if ( $chave != 0 ) {
            if ( $inCodCredorAnterior == $valor['inCodCredor'] ){
                $arDadosPagamento['dados_pagamento'][($chave-1)]['dados_op'][$chave]['op']             = $valor["stOrdemPagamento"];
                $arDadosPagamento['dados_pagamento'][($chave-1)]['dados_op'][$chave]['empenho']        = $valor["stEmpenho"];
                $arDadosPagamento['dados_pagamento'][($chave-1)]['dados_op'][$chave]['valor_bruto']    = $inValorOP;
                $arDadosPagamento['dados_pagamento'][($chave-1)]['dados_op'][$chave]['valor_retencao'] = $nuVlRetencoes;
                $arDadosPagamento['dados_pagamento'][($chave-1)]['dados_op'][$chave]['valor_liquido']  = $inValorLiquidoOP;
                $arDadosPagamento['dados_pagamento'][($chave-1)]['dados_op'][$chave]['observacao']     = $valor['stObservacao'];
                $inValorTotalCredor += $inValorLiquidoOP;
                $arDadosPagamento['dados_pagamento'][($chave-1)]['total_credor'] = $inValorTotalCredor;
            }else{
                $arDadosPagamento['dados_pagamento'][$chave]['credor']                             = $valor["stCredor"];
                $arDadosPagamento['dados_pagamento'][$chave]['cod_credor']                         = $valor["inCodCredor"];
                $arDadosPagamento['dados_pagamento'][$chave]['cpf_cnpj']                           = $inscricao;
                $arDadosPagamento['dados_pagamento'][$chave]['banco_agencia_cc']                   = $valor["inNumBancoCredor"]."/".$valor["inNumAgenciaCredor"]."/".$valor["stNumeroContaCredor"];
                $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['op']             = $valor["stOrdemPagamento"];
                $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['empenho']        = $valor["stEmpenho"];
                $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['valor_bruto']    = $inValorOP;
                $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['valor_retencao'] = $nuVlRetencoes;
                $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['valor_liquido']  = $inValorLiquidoOP;
                $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['observacao']     = $valor['stObservacao'];
                $arDadosPagamento['dados_pagamento'][$chave]['total_credor']                       = $inValorLiquidoOP;
            }
        }else{
            $arDadosPagamento['dados_pagamento'][$chave]['credor']                             = $valor["stCredor"];
            $arDadosPagamento['dados_pagamento'][$chave]['cod_credor']                         = $valor["inCodCredor"];
            $arDadosPagamento['dados_pagamento'][$chave]['cpf_cnpj']                           = $inscricao;
            $arDadosPagamento['dados_pagamento'][$chave]['banco_agencia_cc']                   = $valor["inNumBancoCredor"]."/".$valor["inNumAgenciaCredor"]."/".$valor["stNumeroContaCredor"];
            $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['op']             = $valor["stOrdemPagamento"];
            $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['empenho']        = $valor["stEmpenho"];
            $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['valor_bruto']    = $inValorOP;
            $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['valor_retencao'] = $nuVlRetencoes;
            $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['valor_liquido']  = $inValorLiquidoOP;
            $arDadosPagamento['dados_pagamento'][$chave]['dados_op'][$chave]['observacao']     = $valor['stObservacao'];
            $arDadosPagamento['dados_pagamento'][$chave]['total_credor']                       = $inValorLiquidoOP;

            $inValorTotalCredor += $inValorLiquidoOP;
        }

        $inCodCredorAnterior = $valor['inCodCredor'];
        $inTotalBordero += $inValorLiquidoOP;

    }
    
    $arDadosPagamento['total_bordero'] = $inTotalBordero;
    
    $stDataExtenso = $this->obRConfiguracao->getNomMunicipio() .", ".SistemaLegado::dataExtenso(date('Y-m-d'),false);

    $arDadosPagamento['data_extenso'] = $stDataExtenso;
   
    $inCount = 0;

    $stAssinantes = "";

    for ($x=1; $x<=3; $x++) {

        if ($this->arDadosBordero["inNumAssinante_".$x]) {

            $stAssinantes .= $x . "#";

            $inCount ++;
        }
    }

    if ($stAssinantes) {

        $stAssinantes = substr($stAssinantes,0,strlen($stAssinantes)-1);

        $stAssinantes = explode("#",$stAssinantes);

        if ($inCount == 1) {

            $arDadosPagamento['dados_assinatura'][0]["Assinante_1"] = "";
            $arDadosPagamento['dados_assinatura'][1]["Assinante_1"] = "";
            $arDadosPagamento['dados_assinatura'][2]["Assinante_1"] = "";

            $arDadosPagamento['dados_assinatura'][0]["Assinante_2"] = "";
            $arDadosPagamento['dados_assinatura'][1]["Assinante_2"] = "";
            $arDadosPagamento['dados_assinatura'][2]["Assinante_2"] = "";

            $arDadosPagamento['dados_assinatura'][0]["Assinante_3"] = $this->arDadosBordero["stNomAssinante_".$stAssinantes[0]];
            $arDadosPagamento['dados_assinatura'][1]["Assinante_3"] = $this->arDadosBordero["stCargo_".$stAssinantes[0]];
            $arDadosPagamento['dados_assinatura'][2]["Assinante_3"] = "Matr. " . $this->arDadosBordero["inNumMatricula_".$stAssinantes[0]];

        }
        if ($inCount == 2) {

            $arDadosPagamento['dados_assinatura'][0]["Assinante_1"] = $this->arDadosBordero["stNomAssinante_".$stAssinantes[1]];
            $arDadosPagamento['dados_assinatura'][1]["Assinante_1"] = $this->arDadosBordero["stCargo_".$stAssinantes[1]];
            $arDadosPagamento['dados_assinatura'][2]["Assinante_1"] = "Matr. " . $this->arDadosBordero["inNumMatricula_".$stAssinantes[1]];

            $arDadosPagamento['dados_assinatura'][0]["Assinante_2"] = "";
            $arDadosPagamento['dados_assinatura'][1]["Assinante_2"] = "";
            $arDadosPagamento['dados_assinatura'][2]["Assinante_2"] = "";

            $arDadosPagamento['dados_assinatura'][0]["Assinante_3"] = $this->arDadosBordero["stNomAssinante_".$stAssinantes[0]];
            $arDadosPagamento['dados_assinatura'][1]["Assinante_3"] = $this->arDadosBordero["stCargo_".$stAssinantes[0]];
            $arDadosPagamento['dados_assinatura'][2]["Assinante_3"] = "Matr. " . $this->arDadosBordero["inNumMatricula_".$stAssinantes[0]];

        }
        if ($inCount == 3) {

            for ($x=1; $x<=3; $x++) {

                $arDadosPagamento['dados_assinatura'][0]["Assinante_".$x] =  $this->arDadosBordero["stNomAssinante_".$x];
                $arDadosPagamento['dados_assinatura'][1]["Assinante_".$x] =  $this->arDadosBordero["stCargo_".$x];
                $arDadosPagamento['dados_assinatura'][2]["Assinante_".$x] =  "Matr. " . $this->arDadosBordero["inNumMatricula_".$x];

            }
        }

    } else {
        $arDadosPagamento['dados_assinatura'] = array();
    }

    $arRecordSet = $arDadosPagamento;

    return $obErro;
}



}
