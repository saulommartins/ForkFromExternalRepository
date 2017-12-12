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
  * Página de
  * Data de criação : 05/07/2005

  * @author Analista: Diego Barbosa Victoria
  * @author Programador: Diego Barbosa Victoria

  * @package URBEM
  * @subpackage Relatorio

    * $Id: RContabilidadeRelatorioDiario.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-02.02.23
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                              );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                      );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php"            );

class RContabilidadeRelatorioDiario extends PersistenteRelatorio
{
    /**
        * @var Object
        * @access Private
    */
    public $obTContabilidadeLancamento;
    /**
        * @var String
        * @access Private
    */
    public $stDtInicial;
    /**
        * @var String
        * @access Private
    */
    public $stDtFinal;
    /**
        * @var Integer
        * @access Private
    */
    public $inExercicio;
    /**
        * @var Integer
        * @access Private
    */
    public $inEntidade;

    /**
         * @access Public
         * @param String $valor
    */
    public function setDtInicial($valor) { $this->stDtInicial                           = $valor;}
    /**
         * @access Public
         * @param String $valor
    */
    public function setDtFinal($valor) { $this->stDtFinal                             = $valor;}
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio                           = $valor;}
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setEntidade($valor) { $this->inEntidade                            = $valor;}

    /**
         * @access Public
         * @param String $valor
    */
    public function getDtInicial() { return $this->stDtInicial                 ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getDtFinal() { return $this->stDtFinal                   ; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getExercicio() { return $this->inExercicio                 ; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getEntidade() { return $this->inEntidade                  ; }

    /**
        * Método Construtor
        * @access Private
    */
    public function RContabilidadeRelatorioDiario()
    {
        $this->obTContabilidadeLancamento = new TContabilidadeLancamento;
    }

    /**
        * Método abstrato
        * @access Public
    */
    public function geraRecordSet(&$arRetorno)
    {
        $arRetorno      = array();
        $obErro         = new Erro();
        $obCalendario   = new Calendario();

        $this->obTContabilidadeLancamento->setDado ("exercicio"     , $this->inExercicio);
        $this->obTContabilidadeLancamento->setDado ("cod_entidade"  , $this->inEntidade);
        $this->obTContabilidadeLancamento->setDado ("stDtInicial"   , $this->stDtInicial);
        $this->obTContabilidadeLancamento->setDado ("stDtFinal"     , $this->stDtFinal);

        $obErro = $this->obTContabilidadeLancamento->relatorioDiario( $rsRecordSet );

        //Zerando contadores
        $inCount            = 0;
        $nuSomaDebitoDia    = 0;
        $nuSomaDebitoMes    = 0;
        $nuSomaCreditoDia   = 0;
        $nuSomaCreditoMes   = 0;
        $arRecordSet = array();
        while ( !$rsRecordSet->eof() ) {
            $stData         = $rsRecordSet->getCampo("dt_lote");
            $stContaDebito  = $rsRecordSet->getCampo("cod_estrutural_debito"). " - ".$rsRecordSet->getCampo("nom_conta_debito");
            $stContaCredito = $rsRecordSet->getCampo("cod_estrutural_credito")." - ".$rsRecordSet->getCampo("nom_conta_credito");

            //CONTA DEBITO
            $stValor = str_replace( chr(10), "", $stContaDebito );
            $stValor = wordwrap( $stValor, 75, chr(13) );
            $arValor = explode( chr(13), $stValor );
            $inCountCD = $inCount;
            foreach ($arValor as $stValor) {
                $arRecordSet[$inCountCD]['conta_debito'] = $stValor;
                $inCountCD++;
            }

            //CONTA CREDITO
            $stValor = str_replace( chr(10), "", $stContaCredito );
            $stValor = wordwrap( $stValor, 75, chr(13) );
            $arValor = explode( chr(13), $stValor );
            $inCountCC = $inCount;
            foreach ($arValor as $stValor) {
                $arRecordSet[$inCountCC]['conta_credito'] = $stValor;
                $inCountCC++;
            }

            $arRecordSet[$inCount]["valor_debito"]  = $rsRecordSet->getCampo("vl_lancamento_debito");
            $nuSomaDebitoDia                        = bcadd( $nuSomaDebitoDia , $rsRecordSet->getCampo("vl_lancamento_debito") , 2 );
            $arRecordSet[$inCount]["valor_credito"] = $rsRecordSet->getCampo("vl_lancamento_credito");
            $nuSomaCreditoDia                       = bcadd( $nuSomaCreditoDia , $rsRecordSet->getCampo("vl_lancamento_credito") , 2 );

            if ($inCountCD > $inCountCC) {
                $inCount = $inCountCD;
            } else {
                $inCount = $inCountCC;
            }

            //Histórico
            $stValor = str_replace( chr(10), "", "     ".$rsRecordSet->getCampo("historico") );
            $stValor = wordwrap( $stValor, 150, chr(13) );
            $arValor = explode( chr(13), $stValor );
            foreach ($arValor as $stValor) {
                $arRecordSet[$inCount]['conta_debito'] = $stValor;
                $inCount++;
            }

            $rsRecordSet->proximo();
            if ( $stData != $rsRecordSet->getCampo("dt_lote") ) {
                $arData = explode("/",$stData);
                $inDia  = $arData[0];
                $inMes  = $arData[1];
                $inAno  = $arData[2];

                //$inCount++;
                $arRecordSet[$inCount]["conta_debito"]   = "";
                $arRecordSet[$inCount]["conta_credito"]  = "                          T O T A L   D O   D I A :";
                $arRecordSet[$inCount]["valor_debito"]   = $nuSomaDebitoDia;
                $arRecordSet[$inCount]["valor_credito"]  = $nuSomaCreditoDia;
                $nuSomaDebitoMes    = bcadd( $nuSomaDebitoMes  , $nuSomaDebitoDia , 2 );
                $nuSomaCreditoMes   = bcadd( $nuSomaCreditoMes , $nuSomaCreditoDia , 2 );

                $inUltimoDia = $obCalendario->retornaUltimoDiaMes($inMes,$inAno);
                //echo "$inDia == $inUltimoDia<br>";

                if (!$rsRecordSet->proximo()) {
                    if ( (int) $inDia <= (int) $inUltimoDia ) {

                        if ( !array_key_exists("01/$inMes/$inAno", $arRetorno) ) {
                            $this->obTContabilidadeLancamento->setDado ("stDtInicial"   , "01/$inMes/$inAno");
                            $this->obTContabilidadeLancamento->setDado ("stDtFinal"     , "$inUltimoDia/$inMes/$inAno");

                            $this->obTContabilidadeLancamento->retornaTotalizadorPeriodo( $rsPeriodo );
                            $nuSomaDebitoMes    = $rsPeriodo->getCampo('vl_lancamento_debito');
                            $nuSomaCreditoMes   = $rsPeriodo->getCampo('vl_lancamento_credito');
                        }

                        $inCount++;
                        $arRecordSet[$inCount]["conta_debito"]   = "";
                        $arRecordSet[$inCount]["conta_credito"]  = "            T O T A L   D O   M Ê S :";
                        $arRecordSet[$inCount]["valor_debito"]   = $nuSomaDebitoMes;
                        $arRecordSet[$inCount]["valor_credito"]  = $nuSomaCreditoMes;

                        $nuSomaDebitoMes    = 0;
                        $nuSomaCreditoMes   = 0;
                    }
                } else {
                    if ( (int) $inDia == (int) $inUltimoDia ) {

                        if ( !array_key_exists("01/$inMes/$inAno", $arRetorno) ) {
                            $this->obTContabilidadeLancamento->setDado ("stDtInicial"   , "01/$inMes/$inAno");
                            $this->obTContabilidadeLancamento->setDado ("stDtFinal"     , "$inUltimoDia/$inMes/$inAno");

                            $this->obTContabilidadeLancamento->retornaTotalizadorPeriodo( $rsPeriodo );
                            $nuSomaDebitoMes    = $rsPeriodo->getCampo('vl_lancamento_debito');
                            $nuSomaCreditoMes   = $rsPeriodo->getCampo('vl_lancamento_credito');
                        }

                        $inCount++;
                        $arRecordSet[$inCount]["conta_debito"]   = "";
                        $arRecordSet[$inCount]["conta_credito"]  = "            T O T A L   D O   M Ê S :";
                        $arRecordSet[$inCount]["valor_debito"]   = $nuSomaDebitoMes;
                        $arRecordSet[$inCount]["valor_credito"]  = $nuSomaCreditoMes;

                        $nuSomaDebitoMes    = 0;
                        $nuSomaCreditoMes   = 0;
                    }
                }

                $rsRecordSetData = new RecordSet;
                $rsRecordSetData->preenche( $arRecordSet );
                $rsRecordSetData->addFormatacao('valor_debito','NUMERIC_BR');
                $rsRecordSetData->addFormatacao('valor_credito','NUMERIC_BR');
                $arRetorno[ $stData ]   = $rsRecordSetData;

                //Zerando contadores
                $inCount                = 0;
                $nuSomaDebitoDia        = 0;
                $nuSomaCreditoDia       = 0;
                $arRecordSet            = array();
            }
        }

        return $obErro;
    }
}
