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
    * Classe de Regra de Negócio Empenho
    * Data de Criação   : 01/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    $Revision: 13123 $
    $Name$
    $Autor:$
    $Date: 2006-07-24 11:20:48 -0300 (Seg, 24 Jul 2006) $

    * Casos de uso: uc-03.03.02
                    uc-03.03.01
                    uc-03.03.14
*/

/*
$Log$
Revision 1.18  2006/07/24 14:20:48  tonismar
#6641#

Revision 1.17  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.16  2006/07/06 12:09:31  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php");
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php");
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAlmoxarife.class.php");
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPermissaoAlmoxarifados.class.php");
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php");

/**
    * Classe de Regra de Almoxarife
    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoAlmoxarife
{
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Private
    * @var Boolean
*/
var $boAtivo;

/**
    * @access Private
    * @var Reference Object
*/
var $roUltimoAlmoxarifado;

/**
    * @access Private
    * @var Array
*/
var $arAlmoxarifados;

/**
    * @access Private
    * @var Object
*/
var $obAlmoxarifadoPadrao;

/**
    * @access Public
    * @param String $Valor
*/
function setAtivo($valor) { $this->boAtivo = $valor; }

/**
    * @access Public
    * @return Object
*/
function getAtivo() { return $this->boAtivo; }

/**
     * Método construtor
     * @access Public
*/

function RAlmoxarifadoAlmoxarife()
{
    $this->obTransacao      = new Transacao ;
    $this->obRCGMAlmoxarife = new RUsuario();
    $this->obRCGMAlmoxarife->obRCGM->setNumCGM('');
    $this->obAlmoxarifadoPadrao = new RAlmoxarifadoAlmoxarifado();
}

/**
    * Método para adicionar um Objeto Almoxarifado ao array
    * @access Public
*/
function addAlmoxarifado()
{
   $this->arAlmoxarifados[] = new RAlmoxarifadoAlmoxarifado();
   $this->roUltimoAlmoxarifado = &$this->arAlmoxarifados[ count($this->arAlmoxarifados) - 1];
}

/**
    * @access Public
    * @param RecordSet $obRecordSet
    * @param String $stOrdem
    * @param Boolean $boTransacao
    * @return Erro
*/
function listarTodos(&$rsRecordSet, $stOrder = "", $obTransacao = "")
{
    $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
    $arCodAlmoxarifado = array();
    $stFiltro = '';

    if (!empty($this->arAlmoxarifados)) {
        foreach ($this->arAlmoxarifados as $obAlmoxarifado) {
            $arCodAlmoxarifado[] = $obAlmoxarifado->getCodigo();
        }
    }
    $stCodAlmoxarifado = implode(',', $arCodAlmoxarifado);
    if ($stCodAlmoxarifado) {
       $stFiltro .= " and permissao_almoxarifados.cod_almoxarifado in (".$stCodAlmoxarifado.")";
    }

//incluido este if para fazer filtro no alterar almoxarife
    if (!empty($_REQUEST['inCodAlmoxarifado'])) {
        $stCodigoAlmoxarifado = implode(',', $_REQUEST['inCodAlmoxarifado']);
        $stFiltro .= " and permissao_almoxarifados.cod_almoxarifado in (".$stCodigoAlmoxarifado.")";
    }

//incluido este if para fazer filtro no alterar almoxarife
    if ($_REQUEST['inCodCGMAlmoxarife']) {
        $stFiltro .= " and almoxarife.cgm_almoxarife = ". $_REQUEST['inCodCGMAlmoxarife']." ";
    }

  if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
        $stFiltro .= " and almoxarife.cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM();
    }

  if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
        $stFiltro .= " and almoxarife.cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM();
  }

  if ($this->obRCGMAlmoxarife->getUserName()) {
        $stFiltro .= " and usuario.username iLike '%".$this->obRCGMAlmoxarife->getUserName()."%'";
  }

  if ($this->obRCGMAlmoxarife->obRCGM->getNomCGM()) {
        $stFiltro .= " and lower(sw_cgm2.nom_cgm) iLike lower('%".$this->obRCGMAlmoxarife->obRCGM->getNomCGM()."%')";
  }

    $stFiltro .= " group by almoxarife.cgm_almoxarife, sw_cgm2.nom_cgm, status,ativo,usuario.username ";
    $stOrder = " ORDER BY lower(sw_cgm2.nom_cgm) ";

    $obErro = $obTAlmoxarifadoAlmoxarife->recuperaRelacionamentoTodos( $rsRecordSet, $stFiltro, $stOrder);

    return $obErro;
}

/**
    * @access Public
    * @param RecordSet $obRecordSet
    * @param String $stOrdem
    * @param Boolean $boTransacao
    * @return Erro
*/
function listar(&$rsRecordSet, $stOrder = "", $obTransacao = "")
{
    $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
    $arCodAlmoxarifado = array();
    $stFiltro = '';

    if (!empty($this->arAlmoxarifados)) {
        foreach ($this->arAlmoxarifados as $obAlmoxarifado) {
            $arCodAlmoxarifado[] = $obAlmoxarifado->getCodigo();
        }
    }
    $stCodAlmoxarifado = implode(',', $arCodAlmoxarifado);
    if ($stCodAlmoxarifado) {
       $stFiltro .= " and p.cod_almoxarifado in (".$stCodAlmoxarifado.")";
    }

//incluido este if para fazer filtro no alterar almoxarife
    if (!empty($_REQUEST['inCodAlmoxarifado'])) {
        $stCodigoAlmoxarifado = implode(',', $_REQUEST['inCodAlmoxarifado']);
        $stFiltro .= " and p.cod_almoxarifado in (".$stCodigoAlmoxarifado.")";
    }

//incluido este if para fazer filtro no alterar almoxarife
    if ($_REQUEST['inCodCGMAlmoxarife']) {
        $stFiltro .= " and a1.cgm_almoxarife = ". $_REQUEST['inCodCGMAlmoxarife']." ";
    }

    if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
        $stFiltro .= " and a1.cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM();
    }

    $stFiltro .= " group by a1.cgm_almoxarife, a1_cgm.nom_cgm, status,p.padrao  ";
    $obErro = $obTAlmoxarifadoAlmoxarife->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder);

    return $obErro;
}

/**
    * @access Public
    * @param RecordSet $obRecordSet
    * @param String $stOrdem
    * @param Boolean $boTransacao
    * @return Erro
*/
function listarPermissao(&$rsRecordSet, $stOrder = "",  $boListaPadrao = true, $boTransacao = "")
{
   $obTAlmoxarifadoPermissaoAlmoxarifados = new TAlmoxarifadoPermissaoAlmoxarifados();
   $stFiltro = "";
   if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
      $stFiltro .= " and pa.cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM();
   }
   if (!$boListaPadrao) {
      $stFiltro .= " and pa.padrao = 'f' ";
   }
   $obErro = $obTAlmoxarifadoPermissaoAlmoxarifados->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder);
return $obErro;
}

/**
    * @access Public
    * @param RecordSet $obRecordSet
    * @param String $stOrdem
    * @param Boolean $boTransacao
    * @return Erro
*/
function listarDisponiveis(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
   $obTAlmoxarifadoAlmoxarifado = new TAlmoxarifadoAlmoxarifado();
   $stFiltro = "";
   if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
      $stFiltro .= " and cod_almoxarifado not in ( select cod_almoxarifado from almoxarifado.permissao_almoxarifados pa where pa.cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM(). ")";
   }
   $obErro = $obTAlmoxarifadoAlmoxarifado->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder);

   return $obErro;
}

/**
    * @access Public
    * @param RecordSet $obRecordSet
    * @param String $stOrdem
    * @param Boolean $boTransacao
    * @return Erro
*/
function listarPadrao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $obTAlmoxarifadoPermissaoAlmoxarifados = new TAlmoxarifadoPermissaoAlmoxarifados();
    $stFiltro = "";
    if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
        $stFiltro.= " and pa.cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM();
    }

    $stFiltro .= " and pa.padrao = 't' ";

    $stFiltro .= " group by pa.cod_almoxarifado, sw_cgm.numcgm, sw_cgm.nom_cgm, pa.padrao ";
    $obErro = $obTAlmoxarifadoPermissaoAlmoxarifados->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder );

    return $obErro;
}

/**
    * @access Public
    * @param Obje $boTransacao
    * @return Erro
*/
function consultar($boTransacao = "")
{
   $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
   $rsRecordSet               = new RecordSet;
   
   $obTAlmoxarifadoAlmoxarife->setDado ('cgm_almoxarife', $this->obRCGMAlmoxarife->obRCGM->getNumCGM() );
   $obErro = $obTAlmoxarifadoAlmoxarife->recuperaPorChave( $rsRecordSet, $boTransacao );
   
   if (!$obErro->ocorreu() AND !$rsRecordSet->EOF()) {
      
      $this->obRCGMAlmoxarife->obRCGM->setNumCGM( $rsRecordSet->getCampo( 'cgm_almoxarife' ) );
      $this->setAtivo( $rsRecordSet->getCampo ('ativo'));
      $obTAlmoxarifadoPermissaoAlmoxarifados = new TAlmoxarifadoPermissaoAlmoxarifados;
      $stFiltro = "";
      
      if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
         $stFiltro .= " and pa.cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM();
      }
      if ($this->obRCGMAlmoxarife->obRCGM->getNumCGM()) {
         $stFiltro .= " and pa.padrao = 't'";
      }
      
      $obErro = $obTAlmoxarifadoPermissaoAlmoxarifados->recuperaRelacionamento( $rsRecordSet, $stFiltro, '');
      
      if (!$rsRecordSet->EOF()) {
         $this->obAlmoxarifadoPadrao->setCodigo($rsRecordSet->getCampo('codigo'));
         $this->obAlmoxarifadoPadrao->consultar();
      }
      
   }

   return $obErro;
}

/**
    * @access Public
    * @param Boolean $boTransacao
    * @return Erro
*/
function incluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    
    $obTAlmoxarifadoPermissaoAlmoxarifados = new TAlmoxarifadoPermissaoAlmoxarifados;
    $obTAlmoxarifadoAlmoxarife             = new TAlmoxarifadoAlmoxarife;
    
    if ( !$obErro->ocorreu() ) {
        $obTAlmoxarifadoAlmoxarife->setDado( "ativo"          , $this->boAtivo);
        $obTAlmoxarifadoAlmoxarife->setDado( "cgm_almoxarife" , $this->obRCGMAlmoxarife->obRCGM->getNumCGM() );

        $obErro = $obTAlmoxarifadoAlmoxarife->inclusao( $boTransacao );
        
        if ( !$obErro->ocorreu () ) {
            for ($inCount = 0; $inCount < count($this->arAlmoxarifados); $inCount++) {
                
                $obAlmoxarifado = $this->arAlmoxarifados[$inCount];
                $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "cod_almoxarifado", $obAlmoxarifado->getCodigo());
                $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "cgm_almoxarife", $this->obRCGMAlmoxarife->obRCGM->getNumCGM() );
                $boPadrao = $this->obAlmoxarifadoPadrao->getCodigo() == $obAlmoxarifado->getCodigo();
                $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "padrao", $boPadrao );
                $obErro = $obTAlmoxarifadoPermissaoAlmoxarifados->inclusao( $boTransacao );
                
                if ( $obErro->ocorreu() ) {
                     break;
                }
                
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoAlmoxarife );
    }

    return $obErro;
}

/**
    * @access Public
    * @param Boolean $boTransacao
    * @return Erro
*/
function alterar($boTransacao ="")
{
    $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife;
    $obTAlmoxarifadoPermissaoAlmoxarifados = new TAlmoxarifadoPermissaoAlmoxarifados;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTAlmoxarifadoAlmoxarife->setDado( "ativo"  , $this->boAtivo );
        $obTAlmoxarifadoAlmoxarife->setDado( "cgm_almoxarife"  , $this->obRCGMAlmoxarife->obRCGM->getNumCGM() );

        $obErro = $obTAlmoxarifadoAlmoxarife->alteracao( $boTransacao );
        if ( !$obErro->ocorreu () ) {
            $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "cgm_almoxarife", $this->obRCGMAlmoxarife->obRCGM->getNumCGM());
            $obTAlmoxarifadoPermissaoAlmoxarifados->exclusao( $boTransacao );
            for ($inCount=0; $inCount<count($this->arAlmoxarifados); $inCount++) {
                $obAlmoxarifado = $this->arAlmoxarifados[$inCount];
                $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "cod_almoxarifado", $obAlmoxarifado->getCodigo());
                $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "cgm_almoxarife", $this->obRCGMAlmoxarife->obRCGM->getNumCGM() );
                $boPadrao = $this->obAlmoxarifadoPadrao->getCodigo() == $obAlmoxarifado->getCodigo();
                $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "padrao", $boPadrao );
                $obErro = $obTAlmoxarifadoPermissaoAlmoxarifados->inclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                     break;
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoAlmoxarife );
    }

    return $obErro;

}

/**
    * @access Public
    * @param Boolean $boTransacao
    * @return Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife;
    $obTAlmoxarifadoPermissaoAlmoxarifados = new TAlmoxarifadoPermissaoAlmoxarifados;

    $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;

    $stFiltro = " Where cgm_almoxarife = ".$this->obRCGMAlmoxarife->obRCGM->getNumCGM();
    $obTAlmoxarifadoNaturezaLancamento->recuperaTodos($rsAlmoxarife,$stFiltro);

    if ($rsAlmoxarife->getNumLinhas()<0) {

        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoPermissaoAlmoxarifados->setDado( "cgm_almoxarife", $this->obRCGMAlmoxarife->obRCGM->getNumCGM());
            $obTAlmoxarifadoPermissaoAlmoxarifados->exclusao( $boTransacao );

            if ( !$obErro->ocorreu () ) {
                $obTAlmoxarifadoAlmoxarife->setDado( "cgm_almoxarife", $this->obRCGMAlmoxarife->obRCGM->getNumCGM());
                $obErro = $obTAlmoxarifadoAlmoxarife->exclusao( $boTransacao );
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoAlmoxarife );
        }

    } else {
        $erro = new Erro;
        $erro->setDescricao("Erro");
        $obErro = $erro;
    }

    return $obErro;
}

}