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
     * Classe de regra de negócio para face de quadra
     * Data de Criação: 15/10/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Tonismar Régis Bernardou

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMFaceQuadra.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.9  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_CIM_MAPEAMENTO."TCIMFaceQuadraTrecho.class.php");
include_once (CAM_GT_CIM_MAPEAMENTO."TCIMFaceQuadra.class.php");
include_once (CAM_GT_CIM_MAPEAMENTO."TCIMFaceQuadraAliquota.class.php");
include_once (CAM_GT_CIM_MAPEAMENTO."TCIMFaceQuadraValorM2.class.php");
include_once (CAM_GT_CIM_MAPEAMENTO."TCIMBaixaFaceQuadra.class.php");
include_once (CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
include_once (CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php");

//INCLUDE DAS CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                    );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoFaceQuadraValor.class.php"         );

class RCIMFaceQuadra
{
    public $dtAliquotaVigencia;
    public $inAliquotaCodNorma;
    public $inAliquotaTerritorial;
    public $inAliquotaPredial;
    public $dtMDVigencia;
    public $inMDCodNorma;
    public $inMDTerritorial;
    public $inMDPredial;
    /**
        * @access Private
        * @var String
    */
    public $dtDataBaixa;

    /**
        * @access Private
        * @var Integer
    */
    public $inCodigoFace;
    /**
        * @access Private
        * @var Array
    */
    public $stJustificativa;

    /**
        * @access Private
        * @var Array
    */
    public $stJustificativaReativar;

    /**
        * @access Private
        * @var Array
    */
    public $arTrecho;
    /**
        * @access Private
        * @var Object
    */
    public $obTCIMFaceQuadra;
    /**
        * @access Private
        * @var Object
    */
    public $obRCIMLocalizacao;
    /**
        * @access Private
        * @var Object
    */
    public $obRCIMTrecho;
    /**
        * @access Private
        * @var Object
    */
    public $obRCadastroDinamico;

    public function setAliquotaVigencia($valor) { $this->dtAliquotaVigencia = $valor; }
    public function setAliquotaCodNorma($valor) { $this->inAliquotaCodNorma = $valor; }
    public function setAliquotaTerritorial($valor) { $this->inAliquotaTerritorial = $valor; }
    public function setAliquotaPredial($valor) { $this->inAliquotaPredial = $valor; }
    public function setMDVigencia($valor) { $this->dtMDVigencia = $valor; }
    public function setMDCodNorma($valor) { $this->inMDCodNorma = $valor; }
    public function setMDTerritorial($valor) { $this->inMDTerritorial = $valor; }
    public function setMDPredial($valor) { $this->inMDPredial = $valor; }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setDataBaixa($valor) { $this->dtDataBaixa = $valor;   }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoFace($valor) { $this->inCodigoFace = $valor;   }
    /**
        * @access Public
        * @param String $valor
    */
    public function setJustificativa($valor) { $this->stJustificativa = $valor;   }

    /**
        * @access Public
        * @param String $valor
    */
    public function setJustificativaReativar($valor) { $this->stJustificativaReativar = $valor;   }

    /**
        * @access Public
        * @param Array $valor
    */
    public function setTrecho($valor) { $this->arTrecho = $valor; }
    /**
        * @access Public
        * @param Object $valor
    */
    public function setTCIMFaceQuadra($valor) { $this->obTCIMFaceQuadra = $valor; }
    /**
        * @access Public
        * @param Object $valor
    */
    public function setRCIMLocalizacao($valor) { $this->obRCIMLocalizacao = $valor; }

    /**
        * @access Public
        * @param Object $valor
    */
    public function setRCadastroDinamico($valor) { $this->obRCadastroDinamico = $valor; }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function getDataBaixa() { return $this->dtDataBaixa;   }

    /**
         * @access Public
         * @return Integer
    */
    public function getCodigoFace() { return $this->inCodigoFace;    }

    /**
        * @access Public
        * @return String
    */
    public function getJustificativaReativar() { return $this->stJustificativaReativar;   }

    /**
         * @access Public
         * @return String
    */
    public function getJustificativa() { return $this->stJustificativa;    }
    /**
        * @acces Public
        * @return Array
    */
    public function getTrecho() { return $this->arTrecho; }
    /**
        * @access Public
        * @return Object
    */
    public function getTCIMFaceQuadra() { return $this->obTCIMFaceQuadra; }
    /**
        * @access Public
        * @return Object
    */
    public function getCIMRLocalizacao() { return $this->obRCIMLocalizacao; }
    /**
        * @access Public
        * @return Object
    */
    public function getRCIMTrecho() { return $this->obRCIMTrecho; }

    /**
        * Metodo construtor
        * @access Private
    */
    public function RCIMFaceQuadra()
    {
        $this->obTCIMFaceQuadra         =   new TCIMFaceQuadra;
        $this->obTCIMBaixaFaceQuadra    =   new TCIMBaixaFaceQuadra;
        $this->obTCIMFaceQuadraTrecho   =   new TCIMFaceQuadraTrecho;
        $this->obRCIMLocalizacao        =   new RCIMLocalizacao;
        $this->obRCIMTrecho             =   new RCIMTrecho;
        $this->obRCadastroDinamico      =   new RCadastroDinamico;
        $this->obTransacao              =   new Transacao;
        $this->arTrecho                 =   array();
        $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoFaceQuadraValor );
        $this->obRCadastroDinamico->setCodCadastro          ( 8 );
        $this->obRCadastroDinamico->obRModulo->setCodModulo ( 12 );
     }

     /**
         * Inclui os dados referentes a Face de Quadra
         * @access Public
         * @param Object $obTransacao Parametro Transação
         * @return Object Objeto Erro
     */
     function incluirFaceQuadra($boTransacao = "")
     {
         $boFlagTransacao = false;
         $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
         if ( !$obErro->ocorreu() ) {
             foreach ($this->arTrecho as $obRCIMTrecho) {
                $arCodigoLogradouro = explode(".",$obRCIMTrecho->getCodigoLogradouro());
                $this->obRCIMTrecho->setCodigoTrecho( $obRCIMTrecho->getCodigoTrecho() );
                $this->obRCIMTrecho->setCodigoLogradouro( $arCodigoLogradouro[0] );
                $this->listarFaceQuadraTrecho( $rsFaceQuadraTrecho, $boTransacao );
                if ( !$rsFaceQuadraTrecho->eof() ) {
                    $obErro->setDescricao( "Já existe uma face de quadra para a localização e o trecho selecionado." );
                }
             }
             if ( !$obErro->ocorreu() ) {
                $this->obTCIMFaceQuadra->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                $obErro = $this->obTCIMFaceQuadra->proximoCod( $inCodigoFace, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMFaceQuadra->setDado( "cod_face", $inCodigoFace );
                    $this->obTCIMFaceQuadra->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                    $obErro = $this->obTCIMFaceQuadra->inclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                       if ($this->dtAliquotaVigencia) {
                            $obTCIMFaceQuadraAliquota = new TCIMFaceQuadraAliquota;
                            $obTCIMFaceQuadraAliquota->setDado( "cod_face", $inCodigoFace );
                            $obTCIMFaceQuadraAliquota->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                            $obTCIMFaceQuadraAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                            $obTCIMFaceQuadraAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                            $obTCIMFaceQuadraAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                            $obTCIMFaceQuadraAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                            $obTCIMFaceQuadraAliquota->inclusao( $boTransacao );
                       }

                       if ($this->dtMDVigencia) {
                            $obTCIMFaceQuadraValorM2 = new TCIMFaceQuadraValorM2;
                            $obTCIMFaceQuadraValorM2->setDado( "cod_face", $inCodigoFace );
                            $obTCIMFaceQuadraValorM2->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                            $obTCIMFaceQuadraValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                            $obTCIMFaceQuadraValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                            $obTCIMFaceQuadraValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                            $obTCIMFaceQuadraValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                            $obTCIMFaceQuadraValorM2->inclusao( $boTransacao );
                       }

                       //O Restante dos valores vem setado da página de processamento
                       $arChaveAtributoTrecho =  array( "cod_face" => $inCodigoFace,
                                                        "cod_localizacao" => $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                       $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
                       $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                       if ( !$obErro->ocorreu() ) {
                           foreach ($this->arTrecho as $obRCIMTrecho) {
                               $this->obTCIMFaceQuadraTrecho->setDado( "cod_face", $inCodigoFace );
                               $this->obTCIMFaceQuadraTrecho->setDado( "cod_localizacao" ,$this->obRCIMLocalizacao->getCodigoLocalizacao() );
                               $this->obTCIMFaceQuadraTrecho->setDado( "cod_trecho", $obRCIMTrecho->getCodigoTrecho() );
                               $this->obTCIMFaceQuadraTrecho->setDado( "cod_logradouro",  $obRCIMTrecho->getCodigoLogradouro() );
                               $obErro = $this->obTCIMFaceQuadraTrecho->inclusao( $boTransacao );
                               if ( $obErro->ocorreu() ) {
                                   break;
                               }
                           }
                        }
                    }
                }
             }
         }
         $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMFaceDeQuadra);

         return $obErro;
     }
    /**
        * Altera os dados da Face de Quadra setada
        * @access Public
        * @param Object $obTransacao Parâmetro Transacao
        * @return Object Objeto Erro
    */
    public function alterarFaceQuadra($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $obErro = $this->atualizarFaceQuadraTrecho( $boTransacao );

            if ($this->dtAliquotaVigencia) {
                $obTCIMFaceQuadraAliquota = new TCIMFaceQuadraAliquota;
                $obTCIMFaceQuadraAliquota->setDado( "cod_face", $inCodigoFace );
                $obTCIMFaceQuadraAliquota->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                $obTCIMFaceQuadraAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                $obTCIMFaceQuadraAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                $obTCIMFaceQuadraAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                $obTCIMFaceQuadraAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                $obTCIMFaceQuadraAliquota->inclusao( $boTransacao );
            }

            if ($this->dtMDVigencia) {
                $obTCIMFaceQuadraValorM2 = new TCIMFaceQuadraValorM2;
                $obTCIMFaceQuadraValorM2->setDado( "cod_face", $inCodigoFace );
                $obTCIMFaceQuadraValorM2->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                $obTCIMFaceQuadraValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                $obTCIMFaceQuadraValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                $obTCIMFaceQuadraValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                $obTCIMFaceQuadraValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                $obTCIMFaceQuadraValorM2->inclusao( $boTransacao );
            }
           //$this->obRCadastroDinamico->obPersistenteValores->debug();

           if ( !$obErro->ocorreu() ) {
                $arChaveAtributoFaceQuadra =  array( "cod_face" => $this->inCodigoFace,
                                                     "cod_localizacao" => $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoFaceQuadra );
                $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
           }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMFaceDeQuadra );

        return $obErro;
    }

    /**
        * Exclui os dados da Face de Quadra setada
        * @access Public
        * @param Object $obTransacao Parâmetro Transacao
        * @return Object Objeto Erro
    */
    public function excluirFaceQuadra($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            // excluindo atributos dinamicos
            $arChaveAtributoTrecho = array( 'cod_face'        => $this->inCodigoFace,
                                            'cod_localizacao' => $this->obRCIMLocalizacao->getCodigoLocalizacao() );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
            $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );

            //$this->obRCadastroDinamico->debug();
            if ( !$obErro->ocorreu() ) {

                // excluindo os trechos da face de quadra
                $stTmpChaveFaceQuadraTrecho = $this->obTCIMFaceQuadraTrecho->getComplementoChave();
                $this->obTCIMFaceQuadraTrecho->setComplementoChave( 'cod_face,cod_localizacao');
                $this->obTCIMFaceQuadraTrecho->setDado( "cod_face", $this->inCodigoFace );
                $this->obTCIMFaceQuadraTrecho->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                $this->obTCIMFaceQuadraTrecho->setDado( "cod_trecho", $this->obRCIMTrecho->getCodigoTrecho() );

                $obErro = $this->obTCIMFaceQuadraTrecho->exclusao( $boTransacao );
                //$this->obTCIMFaceQuadraTrecho->debug();

                $this->obTCIMFaceQuadraTrecho->setComplementoChave( $stTmpChaveFaceQuadraTrecho );

                $obTCIMFaceQuadraAliquota = new TCIMFaceQuadraAliquota;
                $obTCIMFaceQuadraAliquota->setDado( "cod_face", $this->inCodigoFace );
                $obTCIMFaceQuadraAliquota->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                $obTCIMFaceQuadraAliquota->exclusao( $boTransacao );

                $obTCIMFaceQuadraValorM2 = new TCIMFaceQuadraValorM2;
                $obTCIMFaceQuadraValorM2->setDado( "cod_face", $this->inCodigoFace );
                $obTCIMFaceQuadraValorM2->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                $obTCIMFaceQuadraValorM2->exclusao( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMBaixaFaceQuadra->setDado( "cod_face", $this->inCodigoFace );
                    $this->obTCIMBaixaFaceQuadra->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                    $obErro = $this->obTCIMBaixaFaceQuadra->exclusao( $boTransacao );

                    // excluindo face de quadra
                    if ( !$obErro->ocorreu() ) {
                        $this->obTCIMFaceQuadra->setDado( "cod_face", $this->inCodigoFace );
                        $this->obTCIMFaceQuadra->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                        $obErro = $this->obTCIMFaceQuadra->exclusao( $boTransacao );
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMFaceQuadra );

        return $obErro;
    }

    /**
        * Efetua a reativacao de uma Face de Quadra setada
        * @access Public
        * @param Object $obTransacao Parâmetro Transacao
        * @return Object Objeto Erro
    */
    public function reativarFaceQuadra($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $dtdiaHOJE = date ("d-m-Y");
            $this->obTCIMBaixaFaceQuadra->setDado( "dt_termino", $dtdiaHOJE );
            $this->obTCIMBaixaFaceQuadra->setDado( "timestamp", $this->dtDataBaixa );
            $this->obTCIMBaixaFaceQuadra->setDado( "cod_face", $this->inCodigoFace );
            $this->obTCIMBaixaFaceQuadra->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
            $this->obTCIMBaixaFaceQuadra->setDado( "justificativa", $this->stJustificativa );
            $this->obTCIMBaixaFaceQuadra->setDado( "justificativa_termino", $this->stJustificativaReativar );
            $obErro = $this->obTCIMBaixaFaceQuadra->alteracao( $boTransacao );
            //$this->obTCIMBaixaFaceQuadra->debug();
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMFaceQuadra );

        return $obErro;
    }

    /**
        * Efetua a baixa em uma Face de Quadra setada
        * @access Public
        * @param Object $obTransacao Parâmetro Transacao
        * @return Object Objeto Erro
    */
    public function baixarFaceQuadra($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $dtdiaHOJE = date ("d-m-Y");
            $this->obTCIMBaixaFaceQuadra->setDado( "dt_inicio", $dtdiaHOJE );
            $this->obTCIMBaixaFaceQuadra->setDado( "cod_face", $this->inCodigoFace );
            $this->obTCIMBaixaFaceQuadra->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
            $this->obTCIMBaixaFaceQuadra->setDado( "justificativa", $this->stJustificativa );
            $obErro = $this->obTCIMBaixaFaceQuadra->inclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMFaceQuadra );

        return $obErro;
    }

    /**
        * Lista os Trechos segundo o filtro setado
        * @access Public
        * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarFaceQuadraTrecho(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->inCodigoFace) {
            $stFiltro .= " AND FQ.cod_face = ".$this->inCodigoFace." ";
        }

        if ( $this->obRCIMTrecho->getCodigoTrecho() ) {
            $stFiltro .= " AND TR.cod_trecho = ".$this->obRCIMTrecho->getCodigoTrecho()." ";
        }

        if ( $this->obRCIMTrecho->getCodigoLogradouro() ) {
            $stFiltro .= " AND TR.cod_logradouro = ".$this->obRCIMTrecho->getCodigoLogradouro()." ";
        }

        if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {

            $stFiltro .= " AND FQ.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->getCodigoLocalizacao()." ";
        }
        $stOrdem = " ORDER BY MNL.nom_logradouro ";
        $obErro = $this->obTCIMFaceQuadra->recuperaFaceQuadraTrecho( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

//        $this->obTCIMFaceQuadra->debug();
        return $obErro;
    }

    /**
        * Lista os Trechos segundo o filtro setado escluindo a face de quadra setada
        * @access Public
        * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarFaceQuadraNaoTrecho(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->inCodigoFace) {
            $stFiltro .= " AND FQ.cod_face <> ".$this->inCodigoFace." ";
        }

        if ( $this->obRCIMTrecho->getCodigoTrecho() ) {
            $stFiltro .= " AND TR.cod_trecho = ".$this->obRCIMTrecho->getCodigoTrecho()." ";
        }

        if ( $this->obRCIMTrecho->getCodigoLogradouro() ) {
            $stFiltro .= " AND TR.cod_logradouro = ".$this->obRCIMTrecho->getCodigoLogradouro()." ";
        }

        if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {

            $stFiltro .= " AND FQ.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->getCodigoLocalizacao()." ";
        }
        $stOrdem = " ORDER BY MNL.nom_logradouro ";
        $obErro = $this->obTCIMFaceQuadra->recuperaFaceQuadraTrecho( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function verificaBaixaFace(&$rsBaixaFace, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->inCodigoFace) {
            $stFiltro .= " AND cod_face = ".$this->inCodigoFace;
        }

        if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
            $stFiltro .= " AND cod_localizacao = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
        }

        $this->obTCIMFaceQuadra->recuperaFaceQuadraBaixa( $rsBaixaFace, $stFiltro, '', $boTransacao );
        //$this->obTCIMFaceQuadra->debug();
        return $obErro;
    }

    /**
        * Lista as Localizacoes segundo o filtro setado
        * @access Public
        * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarFaceQuadra(&$rsRecordSet , $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->inCodigoFace) {
            $stFiltro .= " AND RET.cod_face = ".$this->inCodigoFace." ";
        }

        if ( $this->obRCIMLocalizacao->getValorComposto() ) {
            $stFiltro .= " AND RET.valor_composto like '".$this->obRCIMLocalizacao->getValorComposto()."%' ";
        }

        if ( $this->obRCIMLocalizacao->getNomeLocalizacao() ) {
            $stFiltro .= " AND UPPER(RET.NOM_LOCALIZACAO) LIKE UPPER('".$this->obRCIMLocalizacao->getNomeLocalizacao()."%') ";
        }

        if ( $this->obRCIMTrecho->getNomeLogradouro() ) {
            $stFiltro .= " AND UPPER(TL.nom_tipo||' '||NL.nom_logradouro) LIKE UPPER('".$this->obRCIMTrecho->getNomeLogradouro()."%' )";
        }

        if ($this->obRCIMLocalizacao->inCodigoNivel) {
            $stFiltro .= " AND RET.COD_NIVEL = ".$this->obRCIMLocalizacao->inCodigoNivel." ";
        }

        if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {

            $stFiltro .= " AND RET.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao." ";
        }

        $stOrdem = " ORDER BY RET.cod_face ";
        $obErro = $this->obTCIMFaceQuadra->recuperaFaceQuadra( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        //$this->obTCIMFaceQuadra->debug();
         return $obErro;
     }

      /**
          * Altera os valores dos atributos da face de quadra setado guardando o histórico
          * @access Public
          * @param  Object $obTransacao Parâmetro Transação
          * @return Object Objeto Erro
      */
      function alterarCaracteristicas($boTransacao = "")
      {
         $boFlagTransacao = false;
         $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
         if ( !$obErro->ocorreu() ) {
             //CADASTRO DE ATRIBUTOS
             if ( !$obErro->ocorreu() ) {
                 //O Restante dos valores vem setado da página de processamento
                 $arChaveAtributoFaceQuadra =  array( "cod_face"        => $this->inCodigoFace,
                                                      "cod_localizacao" => $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                 $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoFaceQuadra );
                 $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
             }
         }
         $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMFaceQuadra );

         return $obErro;
     }

     /**
         * Adiciona um objeto Trecho
         * @access Public
         * @param  Array $arChaveTrecho
         * @return Object Objeto Erro
     */
     function addTrecho($arChaveTrecho)
     {
         $obRCIMTrecho = new RCIMTrecho;

         $arTMP = explode(".", $arChaveTrecho['inNumTrecho']);
         $inCodigoLogradouro = $arTMP[0];
         $inSequencia = $arTMP[1];

         if ($_REQUEST['stAcao'] == 'alterar') {
             $obRCIMTrecho->setCodigoLogradouro( $inCodigoLogradouro );
         } else {
             $obRCIMTrecho->setCodigoLogradouro( $arChaveTrecho['inNumTrecho'] );
         }
         $obRCIMTrecho->setCodigoTrecho    ( $arChaveTrecho['inCodigoTrecho'] );
         $obRCIMTrecho->setSequencia       ( $inSequencia );

         $obErro = $obRCIMTrecho->consultarTrecho( $rsRecorSet );

         if ( !$obErro->ocorreu() ) {
             $this->arTrecho[] = $obRCIMTrecho;
         }

         return $obErro;
     }

     /**
         * Faz a verificação se o trecho já esta relacionado a face de quadra e inclui ou exclui da tabela de relacionamento
         * @access Public
         * @param  Object $obTransacao Parâmetro Transação
         * @return Object Objeto Erro
     */
     function atualizarFaceQuadraTrecho($boTransacao = "")
     {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $obErro = $this->listarFaceQuadraTrecho( $rsRecordSet , $boTransacao );

         if ( !$obErro->ocorreu() ) {
             //MONTA UM ARRAY PARA SABER SE ALGUM TRECHO DEVE SER EXCLUIDO
             $arFaceQuadraTrecho = array();
             while ( !$rsRecordSet->eof() ) {
                 $stChave =  $rsRecordSet->getCampo("cod_trecho");
                 $stChave .=".";
                 $stChave .= $rsRecordSet->getCampo("cod_logradouro");
                 $arFaceQuadraTrecho[$stChave] = true;
                 $rsRecordSet->proximo();
             }

             //VERIFICA SE EXISTEM NOVOS TRECHOS PARA SEREM INCLUIDOS E SETA OS QUE NÃO DEVEM SER EXCLUIDOS
             foreach ($this->arTrecho as $obRCIMTrecho) {

                 $stChaveTrecho = $obRCIMTrecho->getCodigoTrecho().".".$obRCIMTrecho->getCodigoLogradouro();

                 if ( !isset( $arFaceQuadraTrecho[$stChaveTrecho]) ) {

                     $this->obRCIMTrecho->setCodigoTrecho( $obRCIMTrecho->getCodigoTrecho() );
                     $this->obRCIMTrecho->setCodigoLogradouro( $obRCIMTrecho->getCodigoLogradouro() );
                     $this->listarFaceQuadraNaoTrecho( $rsFaceQuadraTrecho );
                     if ( !$rsFaceQuadraTrecho->eof() ) {
                         $obErro->setDescricao( "Já existe uma face de quadra para a localização e o trecho selecionado." );
                     }

                     if ( !$obErro->ocorreu() ) {
                         $this->obTCIMFaceQuadraTrecho->setDado( "cod_face",         $this->inCodigoFace );
                         $this->obTCIMFaceQuadraTrecho->setDado( "cod_localizacao",  $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                         $this->obTCIMFaceQuadraTrecho->setDado( "cod_trecho",       $obRCIMTrecho->getCodigoTrecho() );
                         $this->obTCIMFaceQuadraTrecho->setDado( "cod_logradouro",   $obRCIMTrecho->getCodigoLogradouro() );
                         $this->obTCIMFaceQuadraTrecho->setDado( "sequencia",        $obRCIMTrecho->getSequencia() );
                         $obErro = $this->obTCIMFaceQuadraTrecho->inclusao( $boTransacao );
                         if ( $obErro->ocorreu() ) {
                             break;
                         }
                     }
                } else {
                    unset( $arFaceQuadraTrecho[$stChaveTrecho] );
                }
            }
            //EXCLUI OS TRECHOS QUE NÃO FORAM SETADOS
            if ( !$obErro->ocorreu() ) {
                foreach ($arFaceQuadraTrecho as $stChave => $boValor) {
                    $arChave = explode(".",$stChave);
                    $this->obTCIMFaceQuadraTrecho->setDado( "cod_face",         $this->inCodigoFace         );
                    $this->obTCIMFaceQuadraTrecho->setDado( "cod_localizacao",  $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                    $this->obTCIMFaceQuadraTrecho->setDado( "cod_trecho",       $arChave[0] );
                    $this->obTCIMFaceQuadraTrecho->setDado( "cod_logradouro",   $arChave[1] );
                    $obErro = $this->obTCIMFaceQuadraTrecho->exclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMFaceQuadraTrecho );

    return $obErro;
}
}

?>
