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
* Classe de negócio para tratamento de Responsável Técnico
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 10306 $
$Name$
$Author: cercato $
$Date: 2006-05-25 16:12:07 -0300 (Qui, 25 Mai 2006) $

Casos de uso: uc-01.02.98,
              uc-05.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TResponsavelTecnico.class.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."VResponsavelTecnico.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php"                 );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"               );
Include_once    ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                     );
/**
* Classe de Regra de Negócio ResponsavelTecnico
* Data de Criação   : 27/04/2004
* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/

class RResponsavelTecnico extends RCGM
{
    public $inRegistro;
    public $stUfRegistro;
    public $inNumCgm;
    public $inCodigoProfissao;
    public $inCodigoUf;
    public $obTResponsavelTecnico;
    public $obVResponsavelTecnico;
    public $obTUF;
    public $obRProfissao;

    public function setRegistro($valor) { $this->inRegistro            = $valor; }
    public function setUfRegistro($valor) { $this->stUfRegistro          = $valor; }
    public function setNumCgm($valor) { $this->inNumCgm              = $valor; }
    public function setCodigoProfissao($valor) { $this->inCodigoProfissao     = $valor; }
    public function setCodigoUf($valor) { $this->inCodigoUf            = $valor; }
    public function setTResponsavelTecnico($valor) { $this->obTResponsavelTecnico = $valor; }
    public function setVResponsavelTecnico($valor) { $this->obVResponsavelTecnico = $valor; }
    public function setTUf($valor) { $this->obTUF                 = $valor; }
    public function setRProfissao($valor) { $this->obRProfissao          = $valor; }
    public function setRCGM($valor) { $this->obRCGM                = $valor; }

    public function getRegistro() { return $this->inRegistro;            }
    public function getUfRegistro() { return $this->stUfRegistro;          }
    public function getNumCgm() { return $this->inNumCgm;              }
    public function getCodigoProfissao() { return $this->inCodigoProfissao;     }
    public function getCodigoUf() { return $this->inCodigoUf;            }
    public function getTResponsavelTecnico() { return $this->obTResponsavelTecnico; }
    public function getVResponsavelTecnico() { return $this->obVResponsavelTecnico; }
    public function getTUf() { return $this->obTUF;                 }
    public function getRProfissao() { return $this->obRProfissao;          }
    public function getRCGM() { return $this->obRCGM;                }

    public function RResponsavelTecnico()
    {
        $this->setTResponsavelTecnico ( new TResponsavelTecnico );
        $this->setVResponsavelTecnico ( new VResponsavelTecnico );
        $this->setTUf                 ( new TUF                 );
        $this->setRProfissao          ( new RProfissao          );
        $this->setRCGM                ( new RCGM                );
    }

    public function incluirResponsavel($boTransacao = "")
    {
        $this->obTResponsavelTecnico->setDado( "numcgm",        $this->getNumCgm()          );
        $this->obTResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );
        $this->obTResponsavelTecnico->setDado( "num_registro",  $this->getRegistro()        );
        $this->obTResponsavelTecnico->setDado( "cod_uf",        $this->getCodigoUf()        );
        $obErro = $this->obTResponsavelTecnico->inclusao( $boTransacao );

        return $obErro;
    }

    public function alterarResponsavel($boTransacao = "")
    {
        $this->obTResponsavelTecnico->setDado( "numcgm",        $this->getNumCgm()          );
        $this->obTResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );
        $this->obTResponsavelTecnico->setDado( "num_registro",  $this->getRegistro()        );
        $this->obTResponsavelTecnico->setDado( "cod_uf",        $this->getCodigoUf()        );
        $obErro = $this->obTResponsavelTecnico->alteracao( $boTransacao );

        return $obErro;
    }

    public function excluirResponsavel($boTransacao = "")
    {
        $this->obTResponsavelTecnico->setDado( "numcgm",        $this->getNumCgm()          );
        $this->obTResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );
        $obErro = $this->obTResponsavelTecnico->exclusao( $boTransacao );

        return $obErro;
    }

    public function consultarResponsavel($boTransacao = "")
    {
        $this->obVResponsavelTecnico->setDado( "numcgm",        $this->getNumCgm()          );
        $this->obVResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );
        $obErro = $this->obVResponsavelTecnico->recuperaPorChave( $rsListaResponsavel, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setRegistro        ( $rsListaResponsavel->getCampo( "num_registro" )  );
            $this->setUfRegistro      ( $rsListaResponsavel->getCampo( "nom_uf" )        );
            $this->setNumCgm          ( $rsListaResponsavel->getCampo( "numcgm" )        );
            $this->setCodigoProfissao ( $rsListaResponsavel->getCampo( "cod_profissao" ) );
            $this->setCodigoUf        ( $rsListaResponsavel->getCampo( "cod_uf" )        );
            $this->obRCGM->setNomCGM  ( $rsListaResponsavel->getCampo( "nom_cgm")        );
            $this->obRProfissao->setCodigoProfissao( $rsListaResponsavel->getCampo( "cod_profissao" ) );
            $obErro = $this->obRProfissao->consultarProfissao( $boTransacao );
        }

        return $obErro;
    }

    public function listarResponsaveis(&$rsListaResponsalveis, $boTransacao = "")
    {
        $stFiltro = "";
        $stOrdem  = "";
        if ( $this->getCodigoProfissao() ) {
            $stFiltro .= " cod_profissao = ".$this->getCodigoProfissao()." AND ";
        }
        if ( $this->getNumCgm() ) {
            $stFiltro .= " numcgm = ".$this->getNumCGM()." AND ";
        }
        if ( $this->getCodigoUf() ) {
            $stFiltro .= " cod_uf = ".$this->getCodigoUf()." AND ";
        }
        if ( $this->getRegistro() ) {
            $stFiltro .= " num_registro = ".$this->getRegistro()." AND ";
        }
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $obErro = $this->obVResponsavelTecnico->recuperaTodos( $rsListaResponsalveis, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarResponsavelContabil(&$rsResponsavelContabil, $boTrasacao = "")
    {
        $stFiltro = "";
        $stOrdem  = "";
        if ( $this->getCodigoProfissao() ) {
            $stFiltro .= " AND rp.cod_profissao = ".$this->getCodigoProfissao();
        }

        if ( $this->getNumCgm() ) {
//            $stFiltro .= " AND rp.numcgm = ".$this->getNumCGM();
            $stFiltro .= " AND cgm.numcgm = ".$this->getNumCGM();
        }
        if ( $this->obRCGM->getNomCGM() ) {
            $stFiltro .= " AND cgm.nom_cgm like '%".$this->obRCGM->getNomCGM()."%'";
        }
        if ( $this->getCodigoUf() ) {
            $stFiltro .= " AND rp.cod_uf = ".$this->getCodigoUf();
        }
        if ( $this->getRegistro() ) {
            $stFiltro .= " AND rp.num_registro = ".$this->getRegistro();
        }
        $obErro = $this->obTResponsavelTecnico->recuperaResponsavelContabil( $rsResponsavelContabil, $stFiltro, $stOrder, $boTrasacao );

        return $obErro;
    }

    public function listarUF(&$rsUF, $boTransacao = "")
    {
        $stFiltro = "";
        $srOrder  = "ORDER BY nom_uf";
        $obErro = $this->obTUF->recuperaTodos( $rsUF, $stFiltro, $stOrdem, $boTransacao );
    }

    public function consultarNomeResponsavel(&$rsListaResponsavel, $boTransacao = "")
    {
        $this->obVResponsavelTecnico->setDado( "numcgm",        $this->getNumCgm()          );
        $this->obVResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );
        $obErro = $this->obVResponsavelTecnico->recuperaPorChave( $rsListaResponsavel, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setRegistro        ( $rsListaResponsavel->getCampo( "num_registro" )  );
            $this->setUfRegistro      ( $rsListaResponsavel->getCampo( "nom_uf" )        );
            $this->setNumCgm          ( $rsListaResponsavel->getCampo( "numcgm" )        );
            $this->setCodigoProfissao ( $rsListaResponsavel->getCampo( "cod_profissao" ) );
            $this->setCodigoUf        ( $rsListaResponsavel->getCampo( "cod_uf" )        );
            $this->obRCGM->setNomCGM  ( $rsListaResponsavel->getCampo( "nom_cgm")        );
            $this->obRProfissao->setCodigoProfissao( $rsListaResponsavel->getCampo( "cod_profissao" ) );
            $obErro = $this->obRProfissao->consultarProfissao( $boTransacao );
        }

        return $obErro;
    }
}
