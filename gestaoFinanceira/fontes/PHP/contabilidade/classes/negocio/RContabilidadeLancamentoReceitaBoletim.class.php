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
    * Classe de Regra de Negócio Lançamento
    * Data de Criação   : 25/02/2005

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeLancamentoReceitaBoletim.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.05
                    uc-02.02.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."TransacaoSIAM.class.php"           );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php"   );

class RContabilidadeLancamentoReceitaBoletim extends RContabilidadeLancamentoReceita
{
    /**
        * @var Object
        * @access Private
    */
    public $obTransacao;
    /**
        * @var Object
        * @access Private
    */
    public $obTransacaoSIAM;
    /**
        * @var Object
        * @access Private
    */
    public $obTSamlinkSiamNumbol;
    /**
        * @var Object
        * @access Private
    */
    public $obTSamlinkSiamAutent;
    /**
        * @var Object
        * @access Private
    */
    public $obTSamlinkSiamTabrec;
    /**
        * @var Object
        * @access Private
    */
    public $obTSamlinkSiamSaltes;
    /**
        * @var String
        * @access Private
    */
    public $stDataInicial;
    /**
        * @var String
        * @access Private
    */
    public $stDataFinal;
    /**
        * @var Array
        * @access Private
    */
    public $arRContabilidadeLancamentoReceita;
    /**
        * @access Private
        * @var String
    */
    public $stNomLogErros ;
    /**
        * @access Private
        * @var Numeric
    */
    public $obRContabilidadeLancamentoValor;
    /**
        * @var Object
        * @access Private
    */
    public $nuTempoImportacao ;

    public function setDataInicial($valor) { $this->stDataInicial     = $valor; }
    public function setDataFinal($valor) { $this->stDataFinal       = $valor; }
    public function setTempoImportacao($valor) { $this->nuTempoImportacao = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setNomLogErros($valor) { $this->stNomLogErros         = $valor; }

    public function getDataInicial() { return $this->stDataInicial;     }
    public function getDataFinal() { return $this->stDataFinal;       }
    public function getTempoImportacao()
    {
        $hora = (int) ($this->nuTempoImportacao / (60 * 60 ));
        $min = $this->nuTempoImportacao %(60 * 60 );
        $sec = (int) ($min % 60);
        $min = (int) ($min / 60);
        $nuTempoImportacao= str_pad( $hora, 2,"0", STR_PAD_LEFT).":".str_pad( $min, 2,"0", STR_PAD_LEFT).":".str_pad($sec, 2,"0", STR_PAD_LEFT);

        return $nuTempoImportacao;
    }
    /**
        * @access Public
        * @return String
    */
    public function getNomLogErros() { return $this->stNomLogErros;                      }

    public function RContabilidadeLancamentoReceitaBoletim()
    {
        parent::RContabilidadeLancamentoReceita();

        $this->obTransacao          = new Transacao;
        $this->obTransacaoSIAM      = new TransacaoSIAM;
        $this->obRContabilidadeLancamentoValor  = new RContabilidadeLancamentoValor;
        $this->arRContabilidadeLancamentoReceita = array();
    }

    public function verificaDuplicidadeBoletim($boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamNumbol.class.php"         );
        $obTSamlinkSiamNumbol = new TTesourariaSamlinkSiamNumbol;

        $stFiltro  = " WHERE ";
        $stFiltro .= " k18_data >= TO_DATE('".$this->stDataInicial."', 'DD/MM/YYYY') ";
        $stFiltro .= " AND k18_data <= TO_DATE('".$this->stDataFinal."', 'DD/MM/YYYY') ";
        $obErro = $obTSamlinkSiamNumbol->recuperaNumeroRegistros( $rsRecordSet, $stFiltro, "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
             $obErro = $obTSamlinkSiamNumbol->recuperaNumeroRegistrosAgrupado( $rsRecordSetAgrupado, $stFiltro, "", $boTransacao );
             if ( !$obErro->ocorreu() ) {
                 if ( (int) $rsRecordSet->getNumLinhas() > (int) $rsRecordSetAgrupado->getNumLinhas() ) {
                     $obErro->setDescricao( "Existem boletins duplicados no período de ".$this->stDataInicial." a ".$this->stDataFinal."!" );
                 }
             }
        }

        return $obErro;
    }

    public function recuperaBoletins(&$rsBoletins, $boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamNumbol.class.php"         );
        $obTSamlinkSiamNumbol = new TTesourariaSamlinkSiamNumbol;

        $obErro = $this->verificaDuplicidadeBoletim( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stFiltro  = " WHERE ";
            $stFiltro .= "     k18_liber = 't' ";
        //    $stFiltro .= "    AND k18_lanca = 'f' ";
            $stFiltro .= " AND k18_data >= TO_DATE('".$this->stDataInicial."', 'DD/MM/YYYY') ";
            $stFiltro .= " AND k18_data <= TO_DATE('".$this->stDataFinal."', 'DD/MM/YYYY') ";
            $stOrdem = " ORDER BY k18_data ";
            $obErro = $obTSamlinkSiamNumbol->recuperaTodos( $rsBoletins, $stFiltro, $stOrdem, $boTransacao );
        }

        return $obErro;
    }

    public function recuperaReceitas(&$rsReceitas, $stData, $boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamAutent.class.php"         );
        $obTSamlinkSiamAutent = new TTesourariaSamlinkSiamAutent;

        $stFiltro  = " WHERE ";
        $stFiltro .= "     k12_data = TO_DATE('".$stData."', 'dd/mm/yyyy') AND ";
        $stFiltro .= "     k12_empen = '' AND ";
        $stFiltro .= "     k12_rec01 != '' ";
        $obErro = $obTSamlinkSiamAutent->recuperaTodos( $rsReceitas, $stFiltro, "", $boTransacao );

        return $obErro;
    }

    public function recuperaContaReceita(&$inConta, &$stTipo, $inCodigoReceita, $inAnoExe, $boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamTabrec.class.php"         );
        $obTSamlinkSiamTabrec = new TTesourariaSamlinkSiamTabrec;

        $stFiltro  = " WHERE ";
        $stFiltro .= "     k01_codigo = '".$inCodigoReceita."' AND ";
    //    $stFiltro .= "     k01_tipo = 'O' AND ";
        $stFiltro .= "     k01_anoexe = ".$inAnoExe;
        $obErro = $obTSamlinkSiamTabrec->recuperaTodos( $rsTabRec, $stFiltro, "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$rsTabRec->eof() ) {
                $inConta = $rsTabRec->getCampo("k01_conta");
                $stTipo = $rsTabRec->getCampo("k01_tipo");
            } else {
                $obErro->setDescricao( "Houve autenticação com a receita ".$inCodigoReceita." que não esta cadastrada nas receitas do URBEM!" );
            }
        }

        return $obErro;
    }

    public function recuperaEntidadeReceita(&$inCodEntidade, $inConta, $inAnoExe, $boTransacao = "")
    {
         $obRContabLancReceita = new RContabilidadeLancamentoReceita;
         $obRContabLancReceita->obROrcamentoReceita->setCodReceita( $inConta );
         $obRContabLancReceita->obROrcamentoReceita->setExercicio( $inAnoExe );
         $obErro = $obRContabLancReceita->obROrcamentoReceita->consultar( $rsReceitaSiam, $boTransacao );
         if ( !$obErro->ocorreu() ) {
             if ( $rsReceitaSiam->eof() ) {
                 $obErro->setDescricao( "A receita ".$inConta." não esta cadastrada no URBEM para o exercicio ".$inAnoExe."!" );
             } else {
                 $inCodEntidade = $rsReceitaSiam->getCampo( "cod_entidade" );
             }
         }

         return $obErro;
    }

    public function recuperaPlano(&$inPlano, $inConta, $boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamSaltes.class.php"         );
        $obTSamlinkSiamSaltes = new TTesourariaSamlinkSiamSaltes;

         $stFiltro = " WHERE k13_conta = ".$inConta;
         $obErro = $obTSamlinkSiamSaltes->recuperaTodos( $rsSaltes, $stFiltro, "", $boTransacao );
         if ( !$obErro->ocorreu() ) {
             if ( !$rsSaltes->eof() ) {
                 $inPlano = $rsSaltes->getCampo( "k13_plano" );
             } else {
                 $obErro->setDescricao( "Conta ".$inConta." não cadastrada no SIAM!" );
             }
         }

         return $obErro;
    }

    public function importaReceitasSam($boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamNumbol.class.php"         );
        $obTSamlinkSiamNumbol = new TTesourariaSamlinkSiamNumbol;

        $nuHoraInicial = time();
        $obErro = $this->recuperaBoletins( $rsBoletins, $boTransacao );
        if (!$obErro->ocorreu()) {
            if ($rsBoletins->getNumLinhas()<=0) {
                $boErro = true;
                $arBoletins["logErro"] .= "Não há boletim liberado para esta data!";
                $this->logLinha( $arBoletins["logErro"] );
            } else {
                if ( !$obErro->ocorreu() ) {
                    $arBoletins = array();
                    while ( !$rsBoletins->eof() ) {
                        $boErroLog = false;
                        if ( $rsBoletins->getcampo("k18_lanca") == "f" ) {
                            $obErro = $this->recuperaReceitas( $rsReceitas, $rsBoletins->getCampo("k18_data"), $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $arReceita = array();//[conta][rec]
                                while ( !$rsReceitas->eof() ) {
                                    $inAnoExe = substr( $rsReceitas->getCampo("k12_data"), strlen( $rsReceitas->getCampo("k12_data") ) - 4 );
                                    $inConta = $rsReceitas->getCampo("k12_conta");
                                    for ($inCont = 1; $inCont <= 20; $inCont++) {
                                        $stCampoValorReceita = "k12_vlr".str_pad( $inCont, 2, "0", STR_PAD_LEFT);
                                        $stCampoReceita = "k12_rec".str_pad( $inCont, 2, "0", STR_PAD_LEFT);
                                        if ( trim($rsReceitas->getCampo( $stCampoReceita )) ) {
                                            $inReceita = $rsReceitas->getCampo( $stCampoReceita );
                                            $nuValorReceita = $rsReceitas->getCampo( $stCampoValorReceita );
                                            $stLogErro = "\nAutent : ".$rsReceitas->getCampo("k12_autent")."\n";
                                            if ( !is_array($arReceita[$inConta]) ) {
                                                $arReceita[$inConta] = array();
                                            }
                                            if ($nuValorReceita < 0) {
                                                $arReceita[$inConta][$inReceita]["A"] += $nuValorReceita;
                                            } else {
                                                $arReceita[$inConta][$inReceita]["E"] += $nuValorReceita;
                                            }
                                            $arReceita[$inConta][$inReceita]["logErro"] = $stLogErro;
                                        } else {
                                            break;
                                        }
                                    }
                                    $rsReceitas->proximo();
                                }
                            } else {
                                $stLogErro = $obErro->getDescricao();
                                $boErroLog = true;
                            }
                            $arBoletins[$rsBoletins->getcampo("k18_data")] = array( "numero"  => $rsBoletins->getcampo("k18_numero"),
                                                                                    "logErro" => $stLogErro,
                                                                                    "boErroLog" => $boErroLog,
                                                                                    "receita" => $arReceita
                                                                                  );
                        } else {
                            $obErro->setDescricao( "\nBoletim do dia ".$rsBoletins->getCampo("k18_data")." já foi importado!" );
                            $stLogErro = $obErro->getDescricao();
                            $boErroLog = true;
                            $arBoletins[$rsBoletins->getcampo("k18_data")] = array( "numero"  => $rsBoletins->getcampo("k18_numero"),
                                                                                    "logErro" => $stLogErro,
                                                                                    "boErroLog" => $boErroLog,
                                                                                    "receita" => array()
                                                                                  );
                        }
                        $rsBoletins->proximo();
                    }
                    // foreach ($arBoletins as $dtData => $arNumBol) {
                    //     echo "Data = ".$dtData."<br>";
                    //     echo "Numero = ".$arNumBol["numero"]."<br>";
                    //     foreach ($arNumBol["receita"] as $inContaSIAM => $arContaSIAM) {
                    //         echo "Conta = ".$inContaSIAM."<br>";
                    //         foreach ($arContaSIAM as $inReceita => $arValor) {
                    //             echo "Rec cod = ".$inReceita." | valor = ".$arValor["A"]."<br>";
                    //             echo "Rec cod = ".$inReceita." | valor = ".$arValor["E"]."<br>";
                    //         }
                    //     }
                    // }
                    // exit();
                    //PERCORRE O ARRAY DE BOLETINS( NUMBOL )
                    $boErro = false;
                    foreach ($arBoletins as $dtData => $arNumBol) {
                        if ($arNumBol["boErroLog"]) {
                            $this->logLinha( $arNumBol["logErro"] );
                            $boErro = true;
                            continue;
                        }
                        //ABRE TRANSACAO COM O SIAM
                        $boFlagTransacaoSIAM = false;
                        $obErro = $this->obTransacaoSIAM->abreTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM );
                        if ( !$obErro->ocorreu() ) {
                            //ABRE TRANSACAO COM O URBEM
                            $boFlagTransacao = false;
                            $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $inNumeroBoletim = $arNumBol["numero"];
                                $arReceita = $arNumBol["receita"];
                                //PERCORRE O ARRAY DE CONTAS
                                foreach ($arReceita as $inContaSIAM => $arContaSIAM) {
                                    //PERCORRE AS RECEITAS
                                    foreach ($arContaSIAM as $inReceitaSIAM => $arValorSIAM) {
                                        $stLogErro = $arValorSIAM["logErro"];
                                        $stLogErro .= "Data: ".$dtData."\n";
                                        $stLogErro .= "REC01-20: ".$inReceitaSIAM."\n";
                                        $obErro = $this->recuperaContaReceita( $inContaSW, $stTipoSIAM, $inReceitaSIAM, $inAnoExe, $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            if ($stTipoSIAM == 'E') {
                                                continue;
                                            }

                                            $stLogErro .= "Conta (SIAM - SALTES) : ".$inContaSIAM."\n";
                                            $stLogErro .= "Conta (URBEM): ".$inContaSW."\n";
                                            $obErro = $this->recuperaEntidadeReceita( $inCodEntidadeSW, $inContaSW, $inAnoExe, $boTransacao );
                                            if ( !$obErro->ocorreu() ) {
                                                $stLogErro .= "Entidade: ".$inCodEntidade."\n";
                                                $obErro = $this->recuperaPlano( $inPlanoSW, $inContaSIAM, $boTransacao );
                                                if ( !$obErro->ocorreu() ) {
                                                    $stLogErro .= "Plano: ".$inPlanoSW."\n";
                                                    //EXECUTA A ARREACADAÇÃO OU ANULAÇÃO DA RECEITA
                                                    $obRContabLancReceita = new RContabilidadeLancamentoReceita;
                                                    $obRContabLancReceita->obROrcamentoReceita->setCodReceita( $inContaSW );
                                                    $obRContabLancReceita->obROrcamentoReceita->setExercicio( $inAnoExe );
                                                    $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $inAnoExe );
                                                    $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidadeSW );
                                                    $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "A" );
                                                    $obErro = $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo( $boTransacao );
                                                    if ( !$obErro->ocorreu() ) {
                                                        $stNomLote = "Arrecadação da receita Boletim N. ".$inNumeroBoletim;
                                                        $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( $stNomLote );
                                                        $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote ( $dtData );
                                                        $obRContabLancReceita->setContaDebito( $inPlanoSW );
                                                        $obRContabLancReceita->obRContabilidadeLancamento->setBoComplemento ( true );
                                                        $obRContabLancReceita->obRContabilidadeLancamento->setComplemento( $inNumeroBoletim );
                                                        //VERIFICA SE DEVE SER O VALOR DE SER ARRECADADO OU ANULADO
                                                        if ( $arValorSIAM["A"] and !$obErro->ocorreu() ) {//ARRECADA
                                                            $obRContabLancReceita->setValor( -1 * $arValorSIAM["A"] );
                                                            $obErro = $obRContabLancReceita->incluir( $boTransacao );
                                                        }
                                                        if ( $arValorSIAM["E"] and !$obErro->ocorreu() ) {//ESTORNO
                                                            $obRContabLancReceita->setValor( $arValorSIAM["E"] );
                                                            $obRContabLancReceita->setEstorno( true );
                                                            $obErro = $obRContabLancReceita->alterar( $boTransacao );
                                                        }
                                                    }
                                                }
                                            } else {
                                                $boErro = true;
                                                break;
                                            }
                                        }
                                        if ( $obErro->ocorreu() ) {
                                            $boErro = true;
                                            break;
                                        }
                                    }
                                    if ( $obErro->ocorreu() ) {
                                        $boErro = true;
                                        break;
                                    }
                                }
                                if ( !$obErro->ocorreu() ) {
                                    //ALTERAR A TABELA NUMBOL DO SIAM DEIXANDO O CAMPO  k18_lanca COMO TRUE
                                    $obTSamlinkSiamNumbol->setDado('k18_data',   $dtData   );
                                    $obTSamlinkSiamNumbol->setDado('k18_numero', $inNumeroBoletim );
                                    $obTSamlinkSiamNumbol->setDado('k18_liber',  't'  );
                                    $obTSamlinkSiamNumbol->setDado('k18_lanca', 't'  );
                                    $obErro = $obTSamlinkSiamNumbol->alteracao( $boTransacaoSIAM );
                                } else {
                                    //REGISTRAR O ERRO NO LOG
                                    $stLogErro .= $obErro->getDescricao();
                                    $this->logLinha( $stLogErro );
                                }
                            }
                            //fecha trnansacao com o siam com urbem
                            //VERIFICA OCORREU ERRO E FECHA A TRANSACAO COM O SIAM E URBEM
                            if ( $obErro->ocorreu() ) {
                                $boErro = true;
                            }
                        } else {
                           $boErro = true;
                        }

                        $this->obTransacaoSIAM->fechaTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM, $obErro );
                        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                        $boTransacao = "";
                        $boTransacaoSIAM = "";
                    }
                } else {
                    //REGISTRAR O ERRO NO LOG
                    $arBoletins["logErro"] .= $obErro->getDescricao();
                    $this->logLinha( $arBoletins["logErro"] );
                }
            }
        } else {
              //REGISTRAR O ERRO NO LOG
              $arBoletins["logErro"] .= $obErro->getDescricao();
                $this->logLinha( $arBoletins["logErro"] );
         }

        if ( $obErro->ocorreu() or $boErro ) {
            $obErro->setDescricao( "erro" );
        }
        $nuHoraFinal = time();
        $nuTempoTotal = $nuHoraFinal - $nuHoraInicial;
        $this->setTempoImportacao( $nuTempoTotal );

        return $obErro;
    }

    public function importaReceitasSam_($boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamNumbol.class.php"         );
        $obTSamlinkSiamNumbol = new TTesourariaSamlinkSiamNumbol;

        $nuHoraInicial = time();
        $boErro = false;
        $obErro = $this->recuperaBoletins( $rsBoletins, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            //INICIA O LOOP DOS BOLETINS, CADA VOLTA DO LOOP TEM UM TRANSAÇÃO DISTINTA
            while ( !$rsBoletins->eof() ) {
                if ( $rsBoletins->getCampo("k18_lanca") == "f" ) {
                    //ABRE TRANSACAO COM O SIAM
                    $boFlagTransacaoSIAM = false;
                    $obErro = $this->obTransacaoSIAM->abreTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM );
                    if ( !$obErro->ocorreu() ) {
                        //ABRE TRANSACAO COM O URBEM
                        $boFlagTransacao = false;
                        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            //RECUPERA AS RECEITAS AUTENTICADAS DA DATA DO BOLETIM CORRENTE DO LOOP
                            $obErro = $this->recuperaReceitas( $rsReceitas, $rsBoletins->getCampo("k18_data"), $boTransacaoSIAM );
                            if ( !$obErro->ocorreu() ) {
                                //PERCORRE TODAS AS RECEITAS
                                while ( !$rsReceitas->eof() ) {
                                    for ($inCodRec = 1; $inCodRec <= 20; $inCodRec++) {
                                         //MONTA OS NOMES DOS CAMPOS
                                         $stCampoReceita = "k12_rec".str_pad( $inCodRec, 2, "0", STR_PAD_LEFT);
                                         $stCampoValorReceita = "k12_vlr".str_pad( $inCodRec, 2, "0", STR_PAD_LEFT);
                                         $inAnoExe = substr( $rsReceitas->getCampo("k12_data"), strlen( $rsReceitas->getCampo("k12_data") ) - 4 );
                                         $stLogPadrao  = "\nData: ".$rsReceitas->getCampo("k12_data")."\n";
                                         $stLogPadrao .= "Autent : ".$rsReceitas->getCampo("k12_autent")."\n";
                                         $stLogPadrao .= "Conta (SIAM) : ".$rsReceitas->getCampo("k12_conta")."\n";
                                         $stLogPadrao .= "Receita: ".$rsReceitas->getCampo( $stCampoReceita )."\n";
                                         $stLogPadrao .= "Valor: ".$rsReceitas->getCampo($stCampoValorReceita)."\n";
                                         if ( trim( $rsReceitas->getCampo( $stCampoReceita ) ) ) {
                                             //RECUPERA A CONTA NA TABELA TABREC NO SIAM REFERENTE A RECEITA CORRENTE NO FOR ( $stCampoReceita )
                                             $obErro = $this->recuperaContaReceita( $inContaReceita, $stTipo ,$rsReceitas->getCampo( $stCampoReceita ), $inAnoExe, $boTransacaoSIAM );
                                             if ($stTipo == 'E') {
                                                 continue;
                                             }
                                             $stLogPadrao .= "Conta (URBEM): ".$inContaReceita."\n";
                                             if ( !$obErro->ocorreu() ) {
                                                 //RECUPERA A ENTIDADE REFERENTE A CONTA RECUPERADA
                                                 $obErro = $this->recuperaEntidadeReceita( $inCodEntidade, $inContaReceita, $inAnoExe, $boTransacao );
                                                 $stLogPadrao .= "Entidade: ".$inCodEntidade."\n";
                                                 if ( !$obErro->ocorreu() ) {
                                                     //BUSCA O PLANO NA SALTES REFERENTE A CONTA RECUPERADA
                                                     $obErro = $this->recuperaPlano( $inPlano, $rsReceitas->getCampo("k12_conta"), $boTransacao );
                                                     $stLogPadrao .= "Plano: ".$inPlano."\n";
                                                     if ( !$obErro->ocorreu() ) {
                                                         //EXECUTA A ARREACADAÇÃO OU ANULAÇÃO DA RECEITA
                                                         $obRContabLancReceita = new RContabilidadeLancamentoReceita;
                                                         $obRContabLancReceita->obROrcamentoReceita->setCodReceita( $inContaReceita );
                                                         $obRContabLancReceita->obROrcamentoReceita->setExercicio( $inAnoExe );
                                                         $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $inAnoExe );
                                                         $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                                                         $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "A" );
                                                         $obErro = $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo( $boTransacao );
                                                         if ( !$obErro->ocorreu() ) {
                                                             $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( "Arrecadação da receita N. ".$rsBoletins->getCampo("k18_numero") );
                                                             $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote ( $rsBoletins->getCampo("k18_data") );
                                                             $obRContabLancReceita->setContaDebito( $inPlano );
                                                             $obRContabLancReceita->obRContabilidadeLancamento->setBoComplemento ( true );
                                                             $obRContabLancReceita->obRContabilidadeLancamento->setComplemento( $rsBoletins->getCampo("k18_numero") );
                                                             //VERIFICA SE DEVE SER O VALOR DE SER ARRECADADO OU ANULADO
                                                             if ( $rsReceitas->getCampo($stCampoValorReceita) < 0 ) {//ARRECADA
                                                                 $obRContabLancReceita->setValor( -1 * $rsReceitas->getCampo($stCampoValorReceita));
                                                                 $obErro = $obRContabLancReceita->incluir( $boTransacao );
                                                             } else {//ANULA
                                                                 $obRContabLancReceita->setValor( $rsReceitas->getCampo($stCampoValorReceita) );
                                                                 $obErro = $obRContabLancReceita->alterar( $boTransacao );
                                                             }
                                                             if ( $obErro->ocorreu() ) {
                                                                 //REGISTRAR O ERRO NO LOG
                                                                 $stLogPadrao .= $obErro->getDescricao();
                                                                 $this->logLinha( $stLogPadrao );
                                                                 break;
                                                             }
                                                         } else {
                                                             //REGISTRAR O ERRO NO LOG
                                                             $stLogPadrao .= $obErro->getDescricao();
                                                             $this->logLinha( $stLogPadrao );
                                                             break;
                                                         }
                                                     } else {
                                                         //REGISTRAR O ERRO NO LOG
                                                         $stLogPadrao .= $obErro->getDescricao();
                                                         $this->logLinha( $stLogPadrao );
                                                         break;
                                                     }
                                                 } else {
                                                     //REGISTRAR O ERRO NO LOG
                                                     $stLogPadrao .= $obErro->getDescricao();
                                                     $this->logLinha( $stLogPadrao );
                                                     break;
                                                 }
                                             } else {
                                                 //REGISTRAR O ERRO NO LOG
                                                 $stLogPadrao .= $obErro->getDescricao();
                                                 $this->logLinha( $stLogPadrao );
                                                 break;
                                             }
                                         }
                                    }
                                    if ( $obErro->ocorreu() ) {
                                        break;
                                    }
                                    $rsReceitas->proximo();

                               }
                            } elseif ( $obErro->ocorreu() ) {
                                //REGISTRAR O ERRO NO LOG
                                $this->logLinha( $obErro->getDescricao() );
                            }
                        }
                    }
                } else {
                    $obErro->setDescricao( "\nBoletim do dia ".$rsBoletins->getCampo("k18_data")." já foi importado!" );
                    $this->logLinha( $obErro->getDescricao() );
                }
                if ( !$obErro->ocorreu() ) {
                    //ALTERAR A TABELA NUMBOL DO SIAM DEIXANDO O CAMPO  k18_lanca COMO TRUE
                    $obTSamlinkSiamNumbol->setDado('k18_data',   $rsBoletins->getCampo("k18_data")   );
                    $obTSamlinkSiamNumbol->setDado('k18_numero', $rsBoletins->getCampo("k18_numero") );
                    $obTSamlinkSiamNumbol->setDado('k18_liber',  $rsBoletins->getCampo("k18_liber")  );
                    $obTSamlinkSiamNumbol->setDado('k18_lanca', 't'  );
                    $obErro = $obTSamlinkSiamNumbol->alteracao( $boTransacaoSIAM );
                }
                //VERIFICA OCORREU ERRO E FECHA A TRANSACAO COM O SIAM E URBEM
                if ( $obErro->ocorreu() ) {
                    $boErro = true;
                }
                $this->obTransacaoSIAM->fechaTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM, $obErro );
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                $rsBoletins->proximo();
            }
        }
        if ($boErro) {
            $obErro->setDescricao( "erro" );
        }
        $nuHoraFinal = time();
        $nuTempoTotal = $nuHoraFinal - $nuHoraInicial;
        $this->setTempoImportacao( $nuTempoTotal );

        return $obErro;
    }

    public function logCabec()
    {
        $stHoraLog = date( "dmYHis" );
        $this->setNomLogErros("logErros".$stHoraLog.".txt");
        $this->logErros = fopen( CAM_GF_CONTABILIDADE."tmp/".$this->getNomLogErros(), "w");

        fwrite($this->logErros, "+-------------------------------------------------------------------------+\n");
        fwrite($this->logErros, " URBEM \n");
        fwrite($this->logErros, " Importação da arrecadação.\n");
        fwrite($this->logErros, " Log de erros\n");
        fwrite($this->logErros, " Periodo: ".$this->getDataInicial()." a ". $this->getDataFinal()." \n");
        fwrite($this->logErros, "+-------------------------------------------------------------------------+\n\n");
    }
    //
    public function logLinha($stLogObs)
    {
        if (!$this->logErros) {
            $this->logCabec() ;
            $this->boLogErros = true ;
        }
        fwrite($this->logErros, $stLogObs."\n");
    }

    /*
    public function recuperaReceitasSam(&$rsReceitas , $boTransacao = "")
    {
        $stFiltro .= "";
        if ($this->stDataInicial) {
            $stFiltro .= " AND nb.data >= TO_DATE('".$this->stDataInicial."', 'DD/MM/YYYY') ";
        }
        if ($this->stDataFinal) {
            $stFiltro .= " AND nb.data <= TO_DATE('".$this->stDataFinal."', 'DD/MM/YYYY') ";
        }
        $stFiltro .= " AND nb.liberado = 't' ";
        $stFiltro .= " AND nb.lancado = 'f' ";
        $stFiltro .= " AND a.empen = '' ";
        $stFiltro .= " AND a.rec01 > 0 ";
        $stOrdem = " ORDER BY nb.data ";
        $obErro = $this->obVSamlinkSiamNumbol->recuperaRelacionamento( $rsReceitas, $stFiltro, $stOrdem, $boTransacao);
                  $this->obVSamlinkSiamNumbol->debug();

        return $obErro;
    }

    public function importaReceitasSam($boTransacao = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamNumbol.class.php"         );
        $obTSamlinkSiamNumbol = new TTesourariaSamlinkSiamNumbol;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $boFlagTransacaoSIAM = false;
            $obErro = $this->obTransacaoSIAM->abreTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->recuperaReceitasSam( $rsReceitas, $boTransacao );
                while ( !$rsReceitas->eof() and !$obErro->ocorreu() ) {
                    for ($inCodRec = 1; $inCodRec <= 20; $inCodRec++) {
                        $stCampoReceita = "rec".str_pad( $inCodRec, 2, "0", STR_PAD_LEFT);
                        $stCampoValorReceita = "vlr".str_pad( $inCodRec, 2, "0", STR_PAD_LEFT);
                        $stAnoExe = substr( $rsReceitas->getCampo("data"), 0, 4 );
                        if ( trim( $rsReceitas->getCampo( $stCampoReceita ) ) ) {
                            $stFiltro  = " WHERE ";
                            $stFiltro .= "     codigo = '".$rsReceitas->getCampo( $stCampoReceita )."' AND ";
                            $stFiltro .= "     tipo = 'O' AND ";
                            $stFiltro .= "     anoexe = ".$stAnoExe;
                            $obErro = $this->obVSamlinkSiamTabrec->recuperaTodos( $rsTabRec, $stFiltro, "", $boTransacao );
                            if ( !$obErro->ocorreu() and !$rsTabRec->eof() ) {
                                $obRContabLancReceita = new RContabilidadeLancamentoReceita;
                                //SETAR A ENTIDADE DO URBEM
                                $obRContabLancReceita->obROrcamentoReceita->setCodReceita( $rsTabRec->getCampo("conta") );
                                $obRContabLancReceita->obROrcamentoReceita->setExercicio( $stAnoExe );
                                $obErro = $obRContabLancReceita->obROrcamentoReceita->consultar( $rsReceitaSiam, $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    if ( $rsReceitaSiam->eof() ) {
                                        $obErro->setDescricao( "A receita ".$rsTabRec->getCampo("conta")." não esta cadastrada no URBEM para o exercicio ".$stAnoExe."!" );
                                    }
                                }
                                if ( !$obErro->ocorreu() ) {
                                    $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $rsReceitaSiam->getCampo("cod_entidade") );
                                    $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $stAnoExe );
                                    $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "A" );
                                    $obErro = $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo( $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( "Arrecadação da receita N. ".$rsReceitas->getCampo("numero") );
                                        $obRContabLancReceita->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote ( $rsReceitas->getCampo("data_nb") );
                                        //ENTRAR COM ESTE VALOR NA SALTES ( $rsReceitas->getCampo("conta") ) E RECUPERAR
                                        //O VALOR DO 'PLANO' QUE É A CONTA DEBITO
                                        $srFiltro = " WHERE conta = ".$rsReceitas->getCampo("conta");
                                        $obErro = $this->obVSamlinkSiamSaltes->recuperaTodos( $rsSaltes, $srFiltro, "", $boTransacao );
                                        if ( !$obErro->ocorreu() and !$rsSaltes->eof() ) {
                                            $obRContabLancReceita->setContaDebito( $rsSaltes->getCampo("plano") );
                                            $obRContabLancReceita->obRContabilidadeLancamento->setBoComplemento ( true );
                                            $obRContabLancReceita->obRContabilidadeLancamento->setComplemento( $rsReceitas->getCampo("numero") );
                                            if ( $rsReceitas->getCampo($stCampoValorReceita) < 0 ) {
                                                $obRContabLancReceita->setValor( -1 * $rsReceitas->getCampo($stCampoValorReceita));
                                                $obErro = $obRContabLancReceita->incluir( $boTransacao );
                                            } else {
                                                $obRContabLancReceita->setValor( $rsReceitas->getCampo($stCampoValorReceita) );
                                                $obErro = $obRContabLancReceita->alterar( $boTransacao );
                                            }
                                        } elseif ( !$obErro->ocorreu() ) {
                                            $obErro->setDescricao( "Conta ".$rsReceitas->getCampo("conta")." não cadastrada no URBEM!" );
                                            break;
                                        } elseif ( $obErro->ocorreu() ) { break; }
                                    } else { break; }
                                } else { break; }
                            }elseif{
                                 $obErro->setDescricao( "Houve autenticação com a receita ".$rsReceitas->getCampo( $stCampoReceita )." que não esta cadastrada nas receitas do SIAM!" );
                                 break;
                            } elseif ( $obErro->ocorreu() ) { break; }
                        } else { break; }
                    }
                    $stDataNB = $rsReceitas->getCampo("data_nb");
                    $inNumero = $rsReceitas->getCampo("numero");
                    $boLiberado = $rsReceitas->getCampo("liberado");
                    $rsReceitas->proximo();
                    if ( !$obErro->ocorreu() ) {
                        if ( $stDataNB != $rsReceitas->getCampo("data_nb") ) {
                            echo "Alteração na NumBol<br>";
                            //ALTERAR A TABELA NUMBOL DO SIAM DEIXANDO O CAMPO  k18_lanca COMO TRUE
                            $obTSamlinkSiamNumbol->setDado('k18_data',   $stDataNB   );
                            $obTSamlinkSiamNumbol->setDado('k18_numero', $inNumero   );
                            $obTSamlinkSiamNumbol->setDado('k18_liber',  $boLiberado );
                            $obTSamlinkSiamNumbol->setDado('k18_lanca', 't'  );
                            $obErro = $obTSamlinkSiamNumbol->alteracao( $boTransacaoSIAM );
                        }
                    }

                }

            }
        }
    //    if ( !$obErro->ocorreu() ) {
    //        $obErro->setDescricao( "Erro forcado" );
    //    }
        $this->obTransacaoSIAM->fechaTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM, $obErro );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        return $obErro;
    }

    */

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarBoletins( &$rsRecordSet, $stOrder = " ORDER BY substr(nom_lote,34,80),dt_lote ", $boTransacao = "" )
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );
        $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita;

        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() )
            $stFiltro .= " AND exercicio = '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()."' ";
        if( $this->obROrcamentoReceita->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " AND cod_entidade = '".$this->obROrcamentoReceita->obROrcamentoEntidade->getCodigoEntidade()."' ";
        if( $this->getDataInicial() )
            $stFiltro .= " AND dt_lote >= to_date( '".$this->getDataInicial()."', 'dd/mm/yyyy' ) ";
        if( $this->getDataFinal() )
            $stFiltro .= " AND dt_lote <= to_date( '".$this->getDataFinal()."', 'dd/mm/yyyy' ) ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() )
            $stFiltro .= " AND trim(substr(nom_lote,34,80)) = '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote()."' ";
        $obErro = $obTContabilidadeLancamentoReceita->recuperaBoletins($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarBoletinsLote(&$rsRecordSet, $stOrder = " ORDER BY cod_lote ", $boTransacao = "")
    {
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );
        $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita;

        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() )
            $stFiltro .= " AND exercicio= '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()."' ";
        if( $this->obROrcamentoReceita->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " AND cod_entidade = '".$this->obROrcamentoReceita->obROrcamentoEntidade->getCodigoEntidade()."' ";
        if( $this->getDataInicial() )
            $stFiltro .= " AND to_char(dt_lote,'dd/mm/yyyy') >= '".$this->getDataInicial()."' ";
        if( $this->getDataFinal() )
            $stFiltro .= " AND to_char(dt_lote,'dd/mm/yyyy') <= '".$this->getDataFinal()."' ";
        if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() )
            $stFiltro .= " AND rtrim(substr(nom_lote,34,80)) = '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote()."' ";
        $obErro = $obTContabilidadeLancamentoReceita->recuperaBoletinsLote($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    public function excluirBoletim($boTransacao = "")
    {
        $boFlagTransacaoSIAM = false;
        $obErro = $this->obTransacaoSIAM->abreTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM );
        if ( !$obErro->ocorreu() ) {
            //ABRE TRANSACAO COM O URBEM
            $boFlagTransacao = false;
            $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->listarBoletinsLote( $rsBoletins, "", $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    while ( !$rsBoletins->eof() ) {
                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($rsBoletins->getCampo("cod_lote"));
                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio($rsBoletins->getCampo("exercicio"));
                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo($rsBoletins->getCampo("tipo"));
                        $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($rsBoletins->getCampo("cod_entidade"));
                        $this->obROrcamentoReceita->setCodReceita(null);
                        $this->obRContabilidadeLancamento->setSequencia(null);
                        $obErro = $this->obRContabilidadeLancamento->listar( $rsLancamento, "", $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            while ( !$rsLancamento->eof() ) {
                                $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($rsLancamento->getCampo("cod_lote"));
                                $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo($rsLancamento->getCampo("tipo"));
                                $this->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio($rsLancamento->getCampo("exercicio"));
                                $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($rsLancamento->getCampo("cod_entidade"));
                                $this->obROrcamentoReceita->setCodReceita(null);
                                $obErro = $this->listar( $rsLancamentoReceita, "", $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    while ( !$rsLancamentoReceita->eof() ) {
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio($rsLancamento->getCampo("exercicio"));
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($rsLancamentoReceita->getCampo("cod_lote"));
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo($rsLancamentoReceita->getCampo("tipo"));
                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($rsLancamentoReceita->getCampo("cod_entidade"));
                                        $this->obROrcamentoReceita->setCodReceita($rsLancamentoReceita->getCampo("cod_receita"));
                                        $this->obRContabilidadeLancamento->setSequencia($rsLancamentoReceita->getCampo("sequencia"));
                                        $obErro = parent::excluir($boTransacao);
                                        if ( $obErro->ocorreu() ) {
                                            break;
                                        }
                                        $rsLancamentoReceita->proximo();
                                    }
                                } else {
                                    break;
                                }
                                if ( !$obErro->ocorreu() ) {
                                    $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($rsLancamento->getCampo("cod_lote"));
                                    $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo($rsLancamento->getCampo("tipo"));
                                    $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio($rsLancamento->getCampo("exercicio"));
                                    $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($rsLancamento->getCampo("cod_entidade"));
                                    $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia($rsLancamento->getCampo("sequencia"));
                                    $obErro = $this->obRContabilidadeLancamentoValor->excluir( $boTransacao );
                                    if ( $obErro->ocorreu() ) {
                                        break;
                                    }
                                }
                                $rsLancamento->proximo();
                            }
                        }
                    $rsBoletins->proximo();
                    }
                    $obErro = $this->setarLiberacaoBoletim( $boTransacaoSIAM );

                }
            }
        }
        $this->obTransacaoSIAM->fechaTransacao( $boFlagTransacaoSIAM, $boTransacaoSIAM, $obErro );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalEvento  );
        $boTransacao = "";
        $boTransacaoSIAM = "";

        return $obErro;
    }

    public function setarLiberacaoBoletim($boTransacaoSIAM = "")
    {
        include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaSamlinkSiamNumbol.class.php"         );
        $obTSamlinkSiamNumbol = new TTesourariaSamlinkSiamNumbol;

        //ALTERAR A TABELA NUMBOL DO SIAM DEIXANDO O CAMPO  k18_lanca e k18_liber COMO FALSE
        $obTSamlinkSiamNumbol->setDado('k18_data', $this->getDataInicial() );
        $obTSamlinkSiamNumbol->setDado('k18_numero', $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote () );
        $obTSamlinkSiamNumbol->setDado('k18_liber',  'f'  );
        $obTSamlinkSiamNumbol->setDado('k18_lanca', 'f'  );
        $obErro = $obTSamlinkSiamNumbol->alteracao( $boTransacaoSIAM );

        return $obErro;
    }

}
?>
