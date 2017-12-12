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
    * Data de Criação   : 25/01/2006

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30835 $
    $Name$
    $Autor: $
    $Date: 2006-09-19 06:00:01 -0300 (Ter, 19 Set 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.15  2006/09/19 08:52:29  jose.eduardo
Bug #6993#

Revision 1.14  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_FW_PDF."RRelatorio.class.php"                 );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoConfiguracao.class.php" );

/**
    * Classe de Regra de Negócios Transacoes Transferencias
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaRelatorioBorderoTransferencia extends PersistenteRelatorio
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
var $arDadosTransferencias;

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
function setDadosBordero($valor) { $this->arDadosBordero       = $valor; }
/**
     * @access Public
     * @param Array $valor
*/
function setDadosTransferencias($valor) { $this->arDadosTransferencias  = $valor; }

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
function getDadosTransferencias() { return $this->arDadosTransferencias;           }

function RTesourariaRelatorioBorderoTransferencia()
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
    $obRTesourariaBoletim->addArrecadacao();
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( $this->arDadosBordero['stExercicio']);
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setCodigoEntidade($this->arDadosBordero['inCodEntidade'] );
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listar( $rsEntidade );

    $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();

    $obRContabilidadePlanoBanco->setExercicio($this->arDadosBordero['stExercicio'] );
    $obRContabilidadePlanoBanco->setCodPlano($this->arDadosBordero['inCodConta'] );
    $obRContabilidadePlanoBanco->consultar( $boTransacao );

    $arLinha0[0]['DadosBordero']   = "Dados do Borderô de Transferência";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha0);
    $arRecordSet[0] = $rsNewRecord;

    $arLinha1[0]['NumBordero']  = "Número Borderô ";
    $arLinha1[0]['DataBordero'] = str_pad($this->arDadosBordero['inNumBordero'], 3, "0", STR_PAD_LEFT). " / " . $this->arDadosBordero['stExercicio'];
    $arLinha1[1]['NumBordero']  = "Data do Borderô ";
    $arLinha1[1]['DataBordero'] = date("d/m/Y");
    $arLinha1[2]['NumBordero']  = "Entidade ";
    $arLinha1[2]['DataBordero'] = $rsEntidade->getCampo("nom_cgm");
    $arLinha1[3]['NumBordero']  = "Tipo Borderô ";
    $arLinha1[3]['DataBordero'] = "Transferência";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha1);
    $arRecordSet[1] = $rsNewRecord;

    $arLinha2[0]['DadosBoletim']   = "Dados do Boletim";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2);
    $arRecordSet[2] = $rsNewRecord;

    $arLinha3[0]['NumBoletim']  = "Número do Boletim ";
    $arLinha3[0]['DataBoletim'] = str_pad($this->arDadosBordero["inCodBoletim"], 3, "0", STR_PAD_LEFT) . " / " . $this->arDadosBordero["stExercicioBoletim"];
    $arLinha3[1]['NumBoletim']  = "Data do Boletim ";
    $arLinha3[1]['DataBoletim'] = $this->arDadosBordero['stDtBoletim'];
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha3);
    $arRecordSet[3] = $rsNewRecord;

    $arLinha4[0]['AoBanco']   = "Ao Banco ".$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getCodBanco()." / ".$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getNomBanco();
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha4);
    $arRecordSet[4] = $rsNewRecord;

    $arLinha5[0]['Agencia']     = "Agência ";
    $arLinha5[0]['NomAgencia']  = $obRContabilidadePlanoBanco->obRMONAgencia->getCodAgencia() ." / ". $obRContabilidadePlanoBanco->obRMONAgencia->getNomAgencia();
    $arLinha5[1]['Agencia']     = "Conta-Corrente ";
    $arLinha5[1]['NomAgencia']  = $obRContabilidadePlanoBanco->getContaCorrente();
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha5);
    $arRecordSet[5] = $rsNewRecord;

    $arLinha6[0]['Autorizacao']   = "AUTORIZAMOS ESTA AGÊNCIA BANCÁRIA A PAGAR O VALOR TOTAL DESTE BORDERÔ, ATRAVÉS DO DÉBITO EM NOSSA";
    $arLinha6[1]['Autorizacao']   = "CONTA-CORRENTE ACIMA INDICADA. EFETUAR AS TRANSFERÊNCIAS ÀS CONTAS ABAIXO RELACIONADAS.";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    $arRecordSet[6] = $rsNewRecord;

    $arLinha7[0]['Credor']   = "Credor";
    $arLinha7[0]['CNPJ']     = "CPF / CNPJ";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha7);
    $arRecordSet[7] = $rsNewRecord;

    $arLinha8[0]['BancoAgenciaCC']   = "Banco / Agência / C.C";
    $arLinha8[0]['Observacoes']      = "OBSERVAÇÕES";
    $arLinha8[0]['Valor']            = "VALOR";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha8);
    $arRecordSet[8] = $rsNewRecord;

    $inCount = 0;

    $rsTransferencia = new RecordSet;

    array_multisort($this->arDadosTransferencias, SORT_ASC, SORT_STRING);

    $rsTransferencia->preenche($this->arDadosTransferencias);

    $credor = $rsTransferencia->getCampo("inCodCredor");

    $segue = 1;

    $subTotal = 0;

    $totalGeral = 0;

    while (!$rsTransferencia->eof() ) {

          $inValor = str_replace( '.','',$rsTransferencia->getCampo("inValor") );
          $inValor = str_replace( ',','.',$inValor );

        if (strlen($rsTransferencia->getCampo("stCPF/CNPJ")) == 14 ) {
            $obMascara = new Mascara;
            $obMascara->setMascara('99.999.999/9999-99');
            $obMascara->mascaraDado($rsTransferencia->getCampo("stCPF/CNPJ"));
            $inscricao = $obMascara->getMascarado();
        } elseif (strlen($rsTransferencia->getCampo("stCPF/CNPJ")) == 11) {
            $obMascara = new Mascara;
            $obMascara->setMascara('999.999.999-99');
            $obMascara->mascaraDado($rsTransferencia->getCampo("stCPF/CNPJ"));
            $inscricao = $obMascara->getMascarado();
        } else {
            $inscricao = "";
        }

        if ($credor == $rsTransferencia->getCampo('inCodCredor')) {

            if ($segue == 1) {

               $arLinha9[$inCount]['Observacoes']      = $inscricao;
               $arLinha9[$inCount]['Valor']            = "";

               $stCredor      = str_replace( chr(10) , "", $rsTransferencia->getCampo("stCredor") );
               $stCredor      = wordwrap( $stCredor , 50, chr(13) );
               $arCredor      = explode( chr(13), $stCredor );
               foreach ($arCredor as $stCredor) {
                    $arLinha9[$inCount]['BancoAgenciaCC']   = $stCredor;
                    $inCount++;
               }
               $segue = 0;
            }

            $arLinha9[$inCount]['BancoAgenciaCC']   = $rsTransferencia->getCampo("inNumBancoCredor") ." / ". $rsTransferencia->getCampo("inNumAgenciaCredor") ." / ". $rsTransferencia->getCampo("stNumeroContaCredor");
            $arLinha9[$inCount]['Observacoes']      = "Obs.: " . $rsTransferencia->getCampo("stObservacao") ." ". $rsTransferencia->getCampo("inNrDocumento");
            $arLinha9[$inCount]['Valor']            = number_format($inValor,2,',','.');

            $subTotal += $inValor;

            $totalGeral += $inValor;

            $inCount ++;

        } else {

            $arLinha9[$inCount]['BancoAgenciaCC']   = "Total do Credor";
            $arLinha9[$inCount]['Observacoes']      = "";
            $arLinha9[$inCount]['Valor']            = number_format($subTotal,2,',','.');

            $subTotal = 0;

            $arLinha9[$inCount+1]['BancoAgenciaCC']   = "";
            $arLinha9[$inCount+1]['Observacoes']      = "";
            $arLinha9[$inCount+1]['Valor']            = "";

            $arLinha9[$inCount+2]['Observacoes']      = $inscricao;
            $arLinha9[$inCount+2]['Valor']            = "";

            $stCredor      = str_replace( chr(10) , "", $rsTransferencia->getCampo("stCredor") );
            $stCredor      = wordwrap( $stCredor , 50, chr(13) );
            $arCredor      = explode( chr(13), $stCredor );
            foreach ($arCredor as $stCredor) {
                $arLinha9[$inCount+2]['BancoAgenciaCC']   = $stCredor;
                $inCount++;
            }

            $arLinha9[$inCount+2]['BancoAgenciaCC']   = $rsTransferencia->getCampo("inNumBancoCredor") ." / ". $rsTransferencia->getCampo("inNumAgenciaCredor") ." / ". $rsTransferencia->getCampo("stNumeroContaCredor");
            $arLinha9[$inCount+2]['Observacoes']      = "Obs.: " . $rsTransferencia->getCampo("stObservacao") ." ". $rsTransferencia->getCampo("inNrDocumento");
            $arLinha9[$inCount+2]['Valor']            = number_format($inValor,2,',','.');

            $subTotal += $inValor;

            $totalGeral += $inValor;

            $inCount += 3;
        }

        $credor = $rsTransferencia->getCampo("inCodCredor");

        $rsTransferencia->proximo();
    }

    $arLinha9[$inCount]['BancoAgenciaCC']   = "Total do Credor";
    $arLinha9[$inCount]['Observacoes']      = "";
    $arLinha9[$inCount]['Valor']            = number_format($subTotal,2,',','.');

    $arLinha9[$inCount+1]['BancoAgenciaCC']   = "";
    $arLinha9[$inCount+1]['Observacoes']      = "";
    $arLinha9[$inCount+1]['Valor']            = "";

    $arLinha9[$inCount+2]['BancoAgenciaCC']   = "TOTAL DESTE BORDERÔ";
    $arLinha9[$inCount+2]['Observacoes']      = "";
    $arLinha9[$inCount+2]['Valor']            = number_format($totalGeral,2,',','.');

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

                $arLinha11[0]["Assinante_".$x] = $this->arDadosBordero["stNomAssinante_".$stAssinantes[$x-1]];
                $arLinha11[1]["Assinante_".$x] = $this->arDadosBordero["stCargo_".$stAssinantes[$x-1]];
                $arLinha11[2]["Assinante_".$x] = "Matr. " . $this->arDadosBordero["inNumMatricula_".$stAssinantes[$x-1]];
            }
        }

    } else {
        $arLinha11 = array();
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha11);
    $arRecordSet[11] = $rsNewRecord;

    return $obErro;

}

function geraRecordSetBorderoTransferencia(&$arRecordSet , $stOrder = "")
{
    $this->obRConfiguracao->consultarMunicipio();

    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->addBordero();
    $obRTesourariaBoletim->roUltimoBordero->setCodBordero($this->getCodBordero());
    $obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($this->getCodEntidade());
    $obRTesourariaBoletim->roUltimoBordero->setExercicio($this->getExercicio());
    $obRTesourariaBoletim->roUltimoBordero->consultar($rsBordero);

    $obRTesourariaBoletim->roUltimoBordero->addTransacaoTransferencia();
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->setCodBordero($this->getCodBordero());
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->obROrcamentoEntidade->setCodigoEntidade($this->getCodEntidade());
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->setExercicio($this->getExercicio());
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->listar( $rsTransferencia );

    $obRTesourariaBoletim->roUltimoBordero->addAssinatura();
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio( $rsBordero->getCampo("exercicio_bordero") );
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setEntidades( $rsBordero->getCampo("cod_entidade") );
    $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->listar( $rsAssinatura );

    $arLinha0[0]['DadosBordero']   = "Dados do Borderô de Transferência";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha0);
    $arRecordSet[0] = $rsNewRecord;

    $arLinha1[0]['NumBordero']  = "Número Borderô ";
    $arLinha1[0]['DataBordero'] = str_pad($rsBordero->getCampo("cod_bordero"), 3, "0", STR_PAD_LEFT). " / " . $rsBordero->getCampo("exercicio_bordero");
    $arLinha1[1]['NumBordero']  = "Data do Borderô ";
    $arLinha1[1]['DataBordero'] = $rsBordero->getCampo("dt_bordero");
    $arLinha1[2]['NumBordero']  = "Entidade ";
    $arLinha1[2]['DataBordero'] = $rsBordero->getCampo("nom_cgm");
    $arLinha1[3]['NumBordero']  = "Tipo do Borderô ";
    $arLinha1[3]['DataBordero'] = "Transferncia";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha1);
    $arRecordSet[1] = $rsNewRecord;

    $arLinha2[0]['DadosBoletim']   = "Dados do Boletim";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2);
    $arRecordSet[2] = $rsNewRecord;

    $arLinha3[0]['NumBoletim']  = "Número do Boletim ";
    $arLinha3[0]['DataBoletim'] = str_pad($rsBordero->getCampo("cod_boletim"), 3, "0", STR_PAD_LEFT) . " / " . $rsBordero->getCampo("exercicio_boletim");
    $arLinha3[1]['NumBoletim']  = "Data do Boletim ";
    $arLinha3[1]['DataBoletim'] = $rsBordero->getCampo("dt_boletim");
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha3);
    $arRecordSet[3] = $rsNewRecord;

    $arLinha4[0]['AoBanco']   = "Ao Banco ".$rsBordero->getCampo("cod_banco")." / ".$rsBordero->getCampo("nom_banco");
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha4);
    $arRecordSet[4] = $rsNewRecord;

    $arLinha5[0]['Agencia']     = "Agência ";
    $arLinha5[0]['NomAgencia']  = $rsBordero->getCampo("cod_agencia") ." / ". $rsBordero->getCampo("nom_agencia");
    $arLinha5[1]['Agencia']     = "Conta-Corrente ";
    $arLinha5[1]['NomAgencia']  = $rsBordero->getCampo("conta_corrente");
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha5);
    $arRecordSet[5] = $rsNewRecord;

    $arLinha6[0]['Autorizacao']   = "AUTORIZAMOS ESTA AGÊNCIA BANCÁRIA A PAGAR O VALOR TOTAL DESTE BORDERÔ, ATRAVÉS DO DÉBITO EM NOSSA";
    $arLinha6[1]['Autorizacao']   = "CONTA-CORRENTE ACIMA INDICADA. EFETUAR AS TRANSFERÊNCIAS ÀS CONTAS ABAIXO RELACIONADAS.";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    $arRecordSet[6] = $rsNewRecord;

    $arLinha7[0]['Credor']   = "Credor";
    $arLinha7[0]['CNPJ']     = "CPF / CNPJ";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha7);
    $arRecordSet[7] = $rsNewRecord;

    $arLinha8[0]['BancoAgenciaCC']   = "Banco / Agência / C.C";
    $arLinha8[0]['Observacoes']      = "OBSERVAÇÕES";
    $arLinha8[0]['Valor']            = "VALOR";
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha8);
    $arRecordSet[8] = $rsNewRecord;

    $inCount = 0;

    $credor = $rsTransferencia->getCampo("numcgm_transferencia");

    $segue = 1;

    $subTotal = 0;

    $totalGeral = 0;

    while (!$rsTransferencia->eof() ) {

        if (strlen($rsTransferencia->getCampo("inscricao")) == 14 ) {
            $obMascara = new Mascara;
            $obMascara->setMascara('99.999.999/9999-99');
            $obMascara->mascaraDado($rsTransferencia->getCampo("inscricao"));
            $inscricao = $obMascara->getMascarado();
        } elseif (strlen($rsTransferencia->getCampo("inscricao")) == 11) {
            $obMascara = new Mascara;
            $obMascara->setMascara('999.999.999-99');
            $obMascara->mascaraDado($rsTransferencia->getCampo("inscricao"));
            $inscricao = $obMascara->getMascarado();
        } else {
            $inscricao = "";
        }

        if ($credor == $rsTransferencia->getCampo('numcgm_transferencia')) {

            if ($segue == 1) {

                $arLinha9[$inCount]['Observacoes']      = $inscricao;
                $arLinha9[$inCount]['Valor']            = "";

                $stCredor = $rsTransferencia->getCampo("numcgm_transferencia")." - ".$rsTransferencia->getCampo("nom_cgm_transferencia");

                $stCredor      = str_replace( chr(10) , "", $stCredor );
                $stCredor      = wordwrap( $stCredor , 50, chr(13) );
                $arCredor      = explode( chr(13), $stCredor );
                foreach ($arCredor as $stCredor) {
                    $arLinha9[$inCount]['BancoAgenciaCC'] = $stCredor;
                    $inCount++;
                }
                $segue = 0;
            }

            $arLinha9[$inCount]['BancoAgenciaCC']   = $rsTransferencia->getCampo("num_banco_transferencia") ." / ". $rsTransferencia->getCampo("num_agencia_transferencia") ." / ". $rsTransferencia->getCampo("conta_corrente_transferencia");
            $arLinha9[$inCount]['Observacoes']      = "Obs.: ". $rsTransferencia->getCampo("descricao") ." ". $rsTransferencia->getCampo("documento");
            $arLinha9[$inCount]['Valor']            = number_format($rsTransferencia->getCampo("valor"),2,',','.');

            $subTotal += $rsTransferencia->getCampo("valor");

            $totalGeral += $rsTransferencia->getCampo("valor");

            $inCount ++;

        } else {

            $arLinha9[$inCount]['BancoAgenciaCC']   = "Total do Credor";
            $arLinha9[$inCount]['Observacoes']      = "";
            $arLinha9[$inCount]['Valor']            = number_format($subTotal,2,',','.');

            $subTotal = 0;

            $arLinha9[$inCount+1]['BancoAgenciaCC']   = "";
            $arLinha9[$inCount+1]['Observacoes']      = "";
            $arLinha9[$inCount+1]['Valor']            = "";

            $arLinha9[$inCount+2]['Observacoes']      = $inscricao;
            $arLinha9[$inCount+2]['Valor']            = "";

            $stCredor = $rsTransferencia->getCampo("numcgm_transferencia")." - ".$rsTransferencia->getCampo("nom_cgm_transferencia");

            $stCredor      = str_replace( chr(10) , "", $stCredor );
            $stCredor      = wordwrap( $stCredor , 50, chr(13) );
            $arCredor      = explode( chr(13), $stCredor );
            foreach ($arCredor as $stCredor) {
                $arLinha9[$inCount+2]['BancoAgenciaCC'] = $stCredor;
                $inCount++;
            }

            $arLinha9[$inCount+2]['BancoAgenciaCC']   = $rsTransferencia->getCampo("num_banco_transferencia") ." / ". $rsTransferencia->getCampo("num_agencia_transferencia") ." / ". $rsTransferencia->getCampo("conta_corrente_transferencia");
            $arLinha9[$inCount+2]['Observacoes']      = "Obs.: " . $rsTransferencia->getCampo("descricao") ." ". $rsTransferencia->getCampo("documento");
            $arLinha9[$inCount+2]['Valor']            = number_format($rsTransferencia->getCampo("valor"),2,',','.');

            $subTotal += $rsTransferencia->getCampo("valor");

            $totalGeral += $rsTransferencia->getCampo("valor");

            $inCount += 3;
        }

        $credor = $rsTransferencia->getCampo('numcgm_transferencia');

        $rsTransferencia->proximo();
    }

    $arLinha9[$inCount]['BancoAgenciaCC']   = "Total do Credor";
    $arLinha9[$inCount]['Observacoes']      = "";
    $arLinha9[$inCount]['Valor']            = number_format($subTotal,2,',','.');

    $arLinha9[$inCount+1]['BancoAgenciaCC']   = "";
    $arLinha9[$inCount+1]['Observacoes']      = "";
    $arLinha9[$inCount+1]['Valor']            = "";

    $arLinha9[$inCount+2]['BancoAgenciaCC']   = "TOTAL DESTE BORDERÔ";
    $arLinha9[$inCount+2]['Observacoes']      = "";
    $arLinha9[$inCount+2]['Valor']            = number_format($totalGeral,2,',','.');

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

        $arLinha11[0]["Assinante_3"] =  $rsAssinatura->getCampo("nom_cgm");
        $arLinha11[1]["Assinante_3"] =  $rsAssinatura->getCampo("cargo");
        $arLinha11[2]["Assinante_3"] =  "Matr. " . $rsAssinatura->getCampo("num_matricula");

    } elseif ($rsAssinatura->getNumLinhas() == 3) {

        $inCount = 1;

        while (!$rsAssinatura->eof()) {

            $arLinha11[0]["Assinante_".$inCount] = $rsAssinatura->getCampo("nom_cgm");
            $arLinha11[1]["Assinante_".$inCount] = $rsAssinatura->getCampo("cargo");
            $arLinha11[2]["Assinante_".$inCount] = "Matr. " . $rsAssinatura->getCampo("num_matricula");

            $inCount++;
        }
    } else {
        $arLinha11 = array();
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha11);
    $arRecordSet[11] = $rsNewRecord;

    return $obErro;

}

}
