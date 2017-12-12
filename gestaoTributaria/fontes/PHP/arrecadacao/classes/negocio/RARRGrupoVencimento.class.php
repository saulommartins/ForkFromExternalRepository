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
    * Regra de negócio para Grupos de Vencimento
    * Data de Criação   : 19/05/2005

    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @supackage Regras
    * @package Urbem

    * $Id: RARRGrupoVencimento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03

*/

/*
$Log$
Revision 1.16  2007/01/23 15:58:19  fabio
Bug #8055#

Revision 1.15  2006/10/25 18:31:47  cercato
correcao da lista de parcelamento e desconto.

Revision 1.14  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.13  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_ARR_MAPEAMENTO."TARRGrupoVencimento.class.php"    );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRDesconto.class.php"           );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );
include_once (CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php"        );

class RARRGrupoVencimento
{
    /**
        * @access Private
        * @param Boolean
    */
    public $boUtilizarCotaUnica;

    /**
        * @access Private
        * @param Integer
    */
    public $inCodigoVencimento;
    /**
        * @access Private
        * @param String
    */
    public $stDescricao;
    /**
        * @access Private
        * @param Date
    */
    public $dtVencimentoValorIntegral;
    /**
        * @access Private
        * @param Array
    */
    public $arParcelas;
    /**
        * @access Private
        * @param Array
    */
    public $arDescontos;
    /**
        * @access Private
        * @param Object
    */
    public $roRARRCalendarioFiscal;

    /**
        * @access Private
        * @param Float
    */
    public $flLimiteInicial;

    /**
        * @access Public
        * @param Boolean $valor
    */
    public function setUtilizarCotaUnica($valor)
    {
        $this->boUtilizarCotaUnica = $valor;
    }

    /**
        * @access Private
        * @param Float
    */
    public $flLimiteFinal;

    //SETTERS
    /**
        * @access Public
        * @param Float $valor
    */
    public function setLimiteInicial($valor)
    {
        $this->flLimiteInicial = $valor;
    }

    /**
        * @access Public
        * @param Float $valor
    */
    public function setLimiteFinal($valor)
    {
        $this->flLimiteFinal = $valor;
    }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoVencimento($valor)
    {
        $this->inCodigoVencimento = $valor;
    }
    /**
        * @access Public
        * @param String $valor
    */
    public function setDescricao($valor)
    {
        $this->stDescricao = $valor;
    }
    /**
        * @access Public
        * @param Date $valor
    */
    public function setVencimentoValorIntegral($valor)
    {
        $this->dtVencimentoValorIntegral = $valor;
    }
    /**
        * @access Public
        * @param Array $valor
    */
    public function setParcelas($valor)
    {
        $this->arParcelas = $valor;
    }
    /**
        * @access Public
        * @param Array $valor
    */
    public function setDescontos($valor)
    {
        $this->arDescontos = $valor;
    }

    //GETTERS
    /**
        * @access Public
        * @return Boolean
    */
    public function getUtilizarCotaUnica()
    {
        return $this->boUtilizarCotaUnica;
    }

    /**
        * @access Public
        * @return Integer
    */
    public function getCodigoVencimento()
    {
        return $this->inCodigoVencimento;
    }
    /**
        * @access Public
        * @return String
    */
    public function getDescricao()
    {
        return $this->stDescricao;
    }
    /**
        * @access Public
        * @return Date
    */
    public function getVencimentoValorIntegral()
    {
        return $this->dtVencimentoValorIntegral;
    }
    /**
        * @access Public
        * @return Array
    */
    public function getParcelas()
    {
        return $this->arParcelas;
    }
    /**
        * @access Public
        * @return Array
    */
    public function getDescontos()
    {
        return $this->arDescontos;
    }

    /**
        * @access Public
        * @return Float
    */
    public function getLimiteInicial()
    {
        return $this->flLimiteInicial;
    }

    /**
        * @access Public
        * @return Float
    */
    public function getLimiteFinal()
    {
        return $this->flLimiteFinal;
    }

    /**
        * Metodo construtor
        * @access Private
    */
    public function RARRGrupoVencimento(&$obRARRCalendarioFiscal)
    {
        $this->obTransacao             = new Transacao;
        $this->obTARRGrupoVencimento   = new TARRGrupoVencimento;
        $this->obTARRDesconto          = new TARRDesconto;
        $this->obTARRVencimentoParcela = new TARRVencimentoParcela;
        $this->roRARRCalendarioFiscal  = &$obRARRCalendarioFiscal;
        $this->arDescontos             = array();
        $this->arParcelas              = array();
    }

    public function definirDesconto($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->listarDesconto( $rsDesc, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arDesconto = array();
                while ( !$rsDesc->eof() ) {
                    $inChaveDesc = $rsDesc->getCampo( "cod_grupo" ).".".$rsDesc->getCampo( "cod_vencimento" ).".".$rsDesc->getCampo( "cod_desconto");
                    $arDesconto[$inChaveDesc] = true;
                    $rsDesc->proximo();
                }
                foreach ($this->getDescontos() as $arDescontos) {
                    if ($arDescontos['boPercentagem'] == "t") {
                        $flDesconto = str_replace("%","",$arDescontos['flDesconto']);
                    } else {
                        $flDesconto = str_replace("R$","",$arDescontos['flDesconto']);
                    }
                    $obErro = $this->obTARRDesconto->proximoCod( $inCodigoDesconto, $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $inChave = $this->roRARRCalendarioFiscal->getCodigoGrupo().".".$this->getCodigoVencimento().".".$inCodigoDesconto;
                        if ( !isset($arDesconto[$inChave]) ) {
                            $this->obTARRDesconto->setDado( "ano_exercicio", $this->roRARRCalendarioFiscal->getAnoExercicio() );

                            $this->obTARRDesconto->setDado( "cod_grupo"        , $this->roRARRCalendarioFiscal->getCodigoGrupo() );
                            $this->obTARRDesconto->setDado( "cod_vencimento"   , $this->getCodigoVencimento() );
                            $this->obTARRDesconto->setDado( "cod_desconto"     , $inCodigoDesconto );
                            $this->obTARRDesconto->setDado( "data_vencimento"  , $arDescontos['dtVencimento'] );
                            $this->obTARRDesconto->setDado( "valor"            , str_replace(",",".",str_replace(".","",$flDesconto) ) );
                            $this->obTARRDesconto->setDado( "percentual"       , $arDescontos['boPercentagem'] );
                            $obErro = $this->obTARRDesconto->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        } else {
                            unset($arDesconto[$inChave]);
                        }
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    foreach ($arDesconto as $inChaveDesc => $boValor) {
                        $arChave = explode(".",$inChaveDesc);
                        $this->obTARRDesconto->setDado( "cod_grupo"        , $arChave[0] );
                        $this->obTARRDesconto->setDado( "cod_vencimento"   , $arChave[1] );
                        $this->obTARRDesconto->setDado( "cod_desconto"     , $arChave[2] );
                        $this->obTARRDesconto->setDado( "data_vencimento"  , $arDescontos['dtVencimento'] );
                        $this->obTARRDesconto->setDado( "valor"            , str_replace(",",".",str_replace(".","",$flDesconto) ) );
                        $this->obTARRDesconto->setDado( "percentual"       , $arDescontos['boPercentagem'] );
                        $obErro = $this->obTARRDesconto->exclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesconto);

        return $obErro;
    }

    public function definirParcela($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->listarParcela( $rsParc, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTARRVencimentoParcela->setDado( "cod_grupo"      , $rsParc->getCampo( "cod_grupo" ) );
                $this->obTARRVencimentoParcela->setDado( "cod_vencimento" , $rsParc->getCampo( "cod_vencimento" ) );
                if ( $rsParc->getNumLinhas() > 0 ) {
                    $obErro = $this->obTARRVencimentoParcela->exclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    $inConta = 1;
                    foreach ($this->getParcelas() as $arParcelas) {
                        if ($arParcelas['boPercentagem'] == "t") {
                            $flDesconto = str_replace("%","",$arParcelas['flDesconto']);
                        } else {
                            $flDesconto = str_replace("R$","",$arParcelas['flDesconto']);
                        }

                        $this->obTARRVencimentoParcela->setDado( "ano_exercicio"            , $this->roRARRCalendarioFiscal->getAnoExercicio() );
                        $this->obTARRVencimentoParcela->setDado( "cod_grupo"                , $this->roRARRCalendarioFiscal->getCodigoGrupo() );
                        $this->obTARRVencimentoParcela->setDado( "cod_vencimento"           , $this->getCodigoVencimento() );
                        $this->obTARRVencimentoParcela->setDado( "cod_parcela"              , $inConta++ );
                        $this->obTARRVencimentoParcela->setDado( "data_vencimento"          , $arParcelas['dtVencimento'] );
                        if ($arParcelas['dtVencimentoDesc'] != '-') {
                            $this->obTARRVencimentoParcela->setDado( "data_vencimento_desconto" , $arParcelas['dtVencimentoDesc'] );
                        }
                        $this->obTARRVencimentoParcela->setDado( "valor"                    , str_replace(",",".",$flDesconto) );
                        $this->obTARRVencimentoParcela->setDado( "percentual"               , $arParcelas['boPercentagem'] );
                        $obErro = $this->obTARRVencimentoParcela->inclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRVencimentoParcela);

        return $obErro;
    }

    /**
        * Definição de grupo
        * @access Public
    */
    public function definirGrupo($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( count($this->getParcelas()) > 0 ) {
                $obErro = $this->definirParcela( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ( count($this->getDescontos()) > 0 ) {
                        $obErro = $this->definirDesconto( $boTransacao );
                    }
                }
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRGrupoVencimento);

        return $obErro;
    }

    public function listarGrupoVencimento(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->roRARRCalendarioFiscal->getCodigoGrupo() ) {
            $stFiltro .= " COD_GRUPO = ".$this->roRARRCalendarioFiscal->getCodigoGrupo()." AND ";
        }

        if ( $this->roRARRCalendarioFiscal->getAnoExercicio() ) {
            $stFiltro .= " ANO_EXERCICIO = '".$this->roRARRCalendarioFiscal->getAnoExercicio()."' AND ";
        }

        if ( $this->getCodigoVencimento() ) {
            $stFiltro .= " COD_VENCIMENTO = ".$this->getCodigoVencimento()." AND ";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }

        $stOrdem = " ORDER BY COD_GRUPO ";

        $obErro = $this->obTARRGrupoVencimento->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function excluirDesconto($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRDesconto->setDado( "cod_grupo" , $this->roRARRCalendarioFiscal->getCodigoGrupo() );
            $this->obTARRDesconto->setDado( "cod_vencimento" , $this->getCodigoVencimento() );
            $stComp = $this->obTARRDesconto->getComplementoChave();
            $stCod  = $this->obTARRDesconto->getCampoCod();
            $this->obTARRDesconto->setComplementoChave("cod_grupo, cod_vencimento");
            $this->obTARRDesconto->setCampoCod("");
            $obErro = $this->obTARRDesconto->exclusao( $boTransacao );
            $this->obTARRDesconto->setComplementoChave($stComp);
            $this->obTARRDesconto->setCampoCod($stCod);
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRDesconto );

        return $obErro;
    }

    public function listarDesconto(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->roRARRCalendarioFiscal->getCodigoGrupo() ) {
            $stFiltro .= " COD_GRUPO = ".$this->roRARRCalendarioFiscal->getCodigoGrupo()." AND ";
        }

        if ( $this->roRARRCalendarioFiscal->getAnoExercicio() ) {
            $stFiltro .= " ANO_EXERCICIO = '".$this->roRARRCalendarioFiscal->getAnoExercicio()."' AND ";
        }

        if ( $this->getCodigoVencimento() ) {
            $stFiltro .= " COD_VENCIMENTO = ".$this->getCodigoVencimento()." AND ";
        }
/*
        if ( $this->getCodigoDesconto() ) {
            $stFiltro .= " COD_DESCONTO = ".$this->getCodigoDesconto()." AND ";
        }
*/
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }

        $stOrdem = " ORDER BY COD_GRUPO ";

        $obErro = $this->obTARRDesconto->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarParcela(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";

        if ( $this->roRARRCalendarioFiscal->getAnoExercicio() ) {
            $stFiltro .= " ANO_EXERCICIO = '".$this->roRARRCalendarioFiscal->getAnoExercicio()."' AND ";
        }

        if ( $this->roRARRCalendarioFiscal->getCodigoGrupo() ) {
            $stFiltro .= " COD_GRUPO = ".$this->roRARRCalendarioFiscal->getCodigoGrupo()." AND ";
        }

        if ( $this->getCodigoVencimento() ) {
            $stFiltro .= " COD_VENCIMENTO = ".$this->getCodigoVencimento()." AND ";
        }
/*
        if ( $this->getCodigoDesconto() ) {
            $stFiltro .= " COD_PARCELA = ".$this->getCodigoParcela()." AND ";
        }
*/
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }

        $stOrdem = " ORDER BY COD_GRUPO ";

        $obErro = $this->obTARRVencimentoParcela->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function excluirParcela($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRVencimentoParcela->setDado( "cod_grupo" , $this->roRARRCalendarioFiscal->getCodigoGrupo() );
            $this->obTARRVencimentoParcela->setDado( "cod_vencimento" , $this->getCodigoVencimento() );
            $stComp = $this->obTARRVencimentoParcela->getComplementoChave();
            $stCod  = $this->obTARRVencimentoParcela->getCampoCod();
            $this->obTARRVencimentoParcela->setComplementoChave("cod_grupo, cod_vencimento");
            $this->obTARRVencimentoParcela->setCampoCod("");
            $obErro = $this->obTARRVencimentoParcela->exclusao( $boTransacao );
            $this->obTARRVencimentoParcela->setComplementoChave($stComp);
            $this->obTARRVencimentoParcela->setCampoCod($stCod);
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRParcela );

        return $obErro;
    }

    public function excluirGrupoVencimento($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->excluirParcela( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->excluirDesconto( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTARRGrupoVencimento->setDado( "cod_grupo"      , $this->roRARRCalendarioFiscal->getCodigoGrupo() );
                    $stComp = $this->obTARRGrupoVencimento->getComplementoChave();
                    $stCod  = $this->obTARRGrupoVencimento->getCampoCod();
                    $this->obTARRGrupoVencimento->setComplementoChave("cod_grupo");
                    $this->obTARRGrupoVencimento->setCampoCod("");
                    $obErro = $this->obTARRGrupoVencimento->exclusao( $boTransacao );
                    $this->obTARRGrupoVencimento->setComplementoChave($stComp);
                    $this->obTARRGrupoVencimento->setCampoCod($stCod);
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRGrupoVencimento );

        return $obErro;
    }
}
