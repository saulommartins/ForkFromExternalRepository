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
    * Classe de Regra de Negócio Pessoal Contrato
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

      Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"           );
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"           );

/**
    * Classe de Regra de Negócio Pessoal Contrato
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/

class RPessoalContrato
{
    /**
    * @var Integer
    * @access Private
    */
    public $inCodContrato;

    /**
    * @var String
    * @access Private
    */
    public $stRegistro;

    /**
    * @var String
    * @access Private
    */
    public $inDigito;

    /**
    * @var Integer
    * @access Private
    */
    public $obTPessoalContrato;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodContrato($valor) { $this->inCodContrato      = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setRegistro($valor) { $this->stRegistro         = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setDigito($valor) { $this->inDigito          = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalContrato($valor) { $this->obTPessoalContrato = $valor; }

    public function getRegistro() { return $this->stRegistro;                   }
    public function getCodContrato() { return $this->inCodContrato;                }
    public function getDigito() { return $this->inDigito;                     }

    // Método construtor
    public function RPessoalContrato()
    {
        $this->setTPessoalContrato           ( new TPessoalContrato            );
        $this->obMascara                     = new Mascara;
        $this->obRConfiguracaoPessoal        = new RConfiguracaoPessoal;
        $this->obTransacao                       = new Transacao;
    }

    public function incluirContrato($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTPessoalContrato->proximoCod( $inCodContrato, $boTransacao );
            $this->setCodContrato( $inCodContrato );
            $this->obRConfiguracaoPessoal->Consultar( $boTransacao );
            $stMascaraRegistro = $this->obRConfiguracaoPessoal->getMascaraRegistro();
            $boGeracaoRegistro = $this->obRConfiguracaoPessoal->getGeracaoRegistro();
            if ($boGeracaoRegistro == "A") {
                $this->proximoRegistro( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalContrato->setDado( "cod_contrato"       , $this->getCodContrato() );
                $this->obTPessoalContrato->setDado( "registro"           , $this->getRegistro()    );
                $obErro = $this->obTPessoalContrato->inclusao( $boTransacao );
            }
            $this->calculaDigito( $boTransacao );
            $obMascara = new Mascara;
            $stRegistro = $this->getRegistro().$this->getDigito();
            $obMascara->preencheMascaraComZeros2( $stRegistro, $stMascaraRegistro );
            $stRegistro = $obMascara->getMascarado();
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalContrato );

        return $obErro;
    }

    public function excluirContrato($boTransacao = " ")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalContrato->setDado( "cod_contrato"       , $this->inCodContrato );
                //$this->obTPessoalContrato->setDado( "registro"           , $this->stRegistro    );
                $obErro = $this->obTPessoalContrato->exclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalContrato );

        return $obErro;
    }

    public function consultarContrato($boTransacao = " ")
    {
        $boFlagTransacao = false;
        $inCodigo         = $this->obTPessoalContrato->getCampoCod();
        $this->obTPessoalContrato->setCampoCod("registro");
        $this->obTPessoalContrato->setDado( "registro", $this->stRegistro );
        if ($this->stRegistro) {
            $obErro = $this->obTPessoalContrato->recuperaPorChave( $rsRecordSet, $boTransacao );
            $this->obTPessoalContrato->setCampoCod( $inCodigo );
            if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
                $this->setCodContrato( $rsRecordSet->getCampo( "cod_contrato" ));
            }
        }

        return $obErro;
    }

    public function proximoRegistro($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContrato->setCampoCod("registro");
            $obErro = $this->obTPessoalContrato->proximoCod( $stRegistro, $boTransacao);
            if ( !$obErro->ocorreu() ) {
                $this->setRegistro( $stRegistro );
                $this->obTPessoalContrato->setCampoCod("cod_contrato");
            }

        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        return $obErro;
    }

    public function calculaDigito($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalContrato->setDado( "registro", $this->getRegistro() );
            $obErro = $this->obTPessoalContrato->recuperaDigito( $rsRecordSet, "","",$boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->setDigito( $rsRecordSet->getCampo("fn_mod11") );
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        return $obErro;
    }

    public function listarCgmDoRegistro(&$rsRecordset,$inRegistro,$boTransacao="")
    {
        $stFiltro = " AND contrato.registro = ".$inRegistro;
        $obErro = $this->obTPessoalContrato->recuperaCgmDoRegistro($rsRecordset,$stFiltro,"",$boTransacao);

        return $obErro;
    }

}
