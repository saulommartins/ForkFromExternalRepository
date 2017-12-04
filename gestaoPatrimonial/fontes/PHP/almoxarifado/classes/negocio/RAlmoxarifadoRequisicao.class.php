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
  * Classe de Regra de Requisição
  * Data de Criação   : 18/11/2005

  * @author Analista: Diego Victoria Barbosa
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Regra

  * Casos de uso: uc-03.03.11

  */

include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicao.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_GA_ADM_NEGOCIO."RUsuario.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicaoItem.class.php";

/**
 * Classe de Regra de Classificação de Catálogo
 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Leandro André Zis
 */
class RAlmoxarifadoRequisicao
{
    /**
        * @access Private
        * @var Object
    */
    public $obTransacao;

    /**
        * @access Private
        * @var Object
    */
    public $obRCGMRequisitante;

    /**
        * @access Private
        * @var Object
    */
    public $obRCGMSolicitante;

    /**
        * @access Private
        * @var Object
    */
    public $arRAlmoxarifadoRequisicaoItem;

    /**
        * @access Private
        * @var Object
    */
    public $roUltimoRequisicaoItem;

    /**
        * @access Private
        * @var String
    */

    public $stExercicio;

    /**
        * @access Private
        * @var Integer
    */

    public $inCodigo;

    public $inCGMRequisitante;

    public $inCGMSolicitante;

    /**
        * @access Private
        * @var Date
    */

     var $dtDataRequisicao;

     /**
         * @access Private
         * @var String
     */

    public $stObservacao = null;

     /**
         * @access Private
         * @var Object
     */

    public $obRAlmoxarifadoAlmoxarifado;

    /**
         * @access Public
         * @return Boolean
    */
    public $stAcao;

    /**
         * @access Public
         * @return Boolean
    */
    public $boHomologada;

    /**
         * @access Public
         * @return Boolean
     */
   public function setHomologada($valor) { $this->boHomologada = $valor; }

    /**
         * @access Public
         * @return Integer
     */

   public function setExercicio($valor) { $this->stExercicio = $valor; }

    /**
         * @access Public
         * @return Integer
     */

   public function setCodigo($valor) { $this->inCodigo = $valor; }

    /**
         * @access Public
         * @return Date
     */

   public function setDataRequisicao($valor) { $this->dtDataRequisicao = $valor; }

    /**
         * @access Public
         * @return String
     */

   public function setObservacao($valor) { $this->stObservacao = $valor; }

   public function setAcao($valor) { $this->stAcao = $valor; }

    /**
         * @access Public
         * @return Integer
     */

    public function setCGMRequisitante($valor) { $this->inCGMRequisitante = $valor; }

    public function setCGMSolicitante($valor) { $this->inCGMSolicitante = $valor; }

    public function getCGMRequisitante() { return $this->inCGMRequisitante; }

    public function getCGMSolicitante() { return $this->inCGMSolicitante; }

    public function getCodigo() { return $this->inCodigo; }

    /**
         * @access Public
         * @return Date
     */

    public function getDataRequisicao() { return $this->dtDataRequisicao; }

    /**
         * @access Public
         * @return String
     */

    public function getObservacao() { return $this->stObservacao; }

    /**
         * @access Public
         * @return String
     */
    public function getExercicio() { return $this->stExercicio; }

    /**
         * @access Public
         * @return String
     */
    public function getAcao() { return $this->stAcao; }

    /**
         * @access Public
         * @return Boolean
     */
    public function getHomologada() { return $this->boHomologada; }

    /**
         * Método construtor
         * @access Public
    */
    public function RAlmoxarifadoRequisicao()
    {
        $this->obRCGMSolicitante = new RCGM();
        $this->obRCGMRequisitante = new RUsuario();
        $this->obRCGMRequisitante->obRCGM->setNumCGM('');
        $this->obRAlmoxarifadoAlmoxarifado = new RAlmoxarifadoAlmoxarifado();
        //$this->addRequisicaoItem();
        $this->obTransacao  = new Transacao ;
    }

    public function addRequisicaoItem()
    {
       $this->arRAlmoxarifadoRequisicaoItem[] = new RAlmoxarifadoRequisicaoItem($this);
       $this->roUltimoRequisicaoItem = $this->arRAlmoxarifadoRequisicaoItem[count($this->arRAlmoxarifadoRequisicaoItem)-1];
    }

    public function listarRequisicaoItemConsultar(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {

        if ( $this->getCodigo() ) {
            $stFiltro .= " AND req.cod_requisicao = ". $this->getCodigo();
        }

        if ( $this->getExercicio() ) {
            $stFiltro .= " AND req.exercicio = '".$this->getExercicio()."' ";
        }

        if ( $this->obRAlmoxarifadoAlmoxarifado->getCodigo() ) {
            $stFiltro .= " AND req.cod_almoxarifado IN (".$this->obRAlmoxarifadoAlmoxarifado->getCodigo().") ";
        }

        if ( $this->getObservacao() ) {
            $stFiltro .= " AND req.observacao ILIKE '".$this->getObservacao()."' ";
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo() ) {
            $stFiltro .= " AND reqi.cod_item = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo();
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo() ) {
            $stFiltro .= " and reqi.cod_marca = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo();
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() ) {
            $stFiltro .= " AND reqi.cod_centro = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo();
        }

        if ( $this->getCGMSolicitante() ) {
            $stFiltro .= " AND req.cgm_solicitante = ".$this->getCGMSolicitante();
        }

        if ( $this->getCGMRequisitante() ) {
            $stFiltro .= " AND req.cgm_requisitante = ".$this->getCGMRequisitante();
        }

        if ($stFiltro != "") {
            $stFiltro = " WHERE ".substr($stFiltro,5);
        }

        $stOrdem .= " GROUP BY  req.exercicio                \n";
        $stOrdem .= "        ,  req.cod_almoxarifado         \n";
        $stOrdem .= "        ,  req.cod_requisicao           \n";
        $stOrdem .= "        ,  req.observacao               \n";
        $stOrdem .= "        ,  req.dt_requisicao            \n";
        $stOrdem .= "        ,  req.cgm_solicitante          \n";
        $stOrdem .= "        ,  cgm2.nom_cgm                 \n";
        $stOrdem .= "        ,  req.cgm_requisitante         \n";
        $stOrdem .= "        ,  cgm3.nom_cgm                 \n";
        $stOrdem .= "        ,  cgm.nom_cgm                  \n";

        if (empty($stOrder)) {
            $stOrdem .= " ORDER BY req.cod_requisicao \n";
        } else {
            $stOrdem .= " ORDER BY ".$stOrder;
        }

        $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
        $obErro = $obTAlmoxarifadoRequisicao->recuperaRequisicaoItemConsultar($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;

    }

    public function listarRequisicaoItem(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        $this->addRequisicaoItem();

        if ( $this->getCodigo() ) {
            $stFiltro .= " and req.cod_requisicao = ". $this->getCodigo();
        }

        if ( $this->getExercicio() ) {
            $stFiltro .= " and req.exercicio = '".$this->getExercicio()."' ";
        }

        if ( $this->obRAlmoxarifadoAlmoxarifado->getCodigo() ) {
            $stFiltro .= " and req.cod_almoxarifado in (".$this->obRAlmoxarifadoAlmoxarifado->getCodigo().") ";
        }

        if ( $this->getObservacao() ) {
            $stFiltro .= " and req.observacao ilike '".$this->getObservacao()."' ";
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo() ) {
            $stFiltro .= " and reqi.cod_item = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo();
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo() ) {
            $stFiltro .= " and reqi.cod_marca = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo();
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() ) {
            $stFiltro .= " and reqi.cod_centro = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo();
        }

        if ( $this->getCGMSolicitante() ) {
            $stFiltro .= " AND req.cgm_solicitante = ".$this->getCGMSolicitante();
        }

        if ( $this->getCGMRequisitante() ) {
            $stFiltro .= " AND req.cgm_requisitante = ".$this->getCGMRequisitante();
        }

        $stOrdem .= " group by                    \n";
        $stOrdem .= "     req.exercicio           \n";
        $stOrdem .= "     ,req.cod_almoxarifado   \n";
        $stOrdem .= "     ,req.cod_requisicao     \n";
        $stOrdem .= "     ,req.cgm_requisitante   \n";
        $stOrdem .= "     ,req.cgm_solicitante    \n";
        $stOrdem .= "     ,req.dt_requisicao      \n";
        $stOrdem .= "     ,req.observacao         \n";
        $stOrdem .= "     ,req.motivo             \n";
        $stOrdem .= "     ,cgm.nom_cgm            \n";
        $stOrdem .= "     ,cgm.numcgm             \n";
        $stOrdem .= "     ,cgm2.nom_cgm           \n";
        $stOrdem .= "     ,cgm2.numcgm            \n";

        $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
        if ( $this->getAcao() == 'anular' ) {
            $obTAlmoxarifadoRequisicao->setDado('acao', 'anular' );
        } elseif ( $this->getAcao() == 'excluir' ) {
            $obTAlmoxarifadoRequisicao->setDado('acao', 'excluir' );
        } elseif ( $this->getAcao() == 'alterar' ) {
            $obTAlmoxarifadoRequisicao->setDado('acao', 'alterar' );
        }

        $obErro = $obTAlmoxarifadoRequisicao->recuperaRequisicaoItem($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;

    }

    public function listarRequisicaoAlteracao(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {

        if ( $this->getCodigo() ) {
            $stFiltro .= " and req.cod_requisicao = ". $this->getCodigo();
        }

        if ( $this->getExercicio() ) {
            $stFiltro .= " and req.exercicio = '".$this->getExercicio()."' ";
        }

        if ( $this->obRAlmoxarifadoAlmoxarifado->getCodigo() ) {
            $stFiltro .= " and req.cod_almoxarifado in (".$this->obRAlmoxarifadoAlmoxarifado->getCodigo().") ";
        }

        if ( $this->getObservacao() ) {
            $stFiltro .= " and req.observacao ilike '".$this->getObservacao()."' ";
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo() ) {
            $stFiltro .= " and reqi.cod_item = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo();
        }
        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo() ) {
            $stFiltro .= " and reqi.cod_marca = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo();
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() ) {
            $stFiltro .= " and reqi.cod_centro = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo();
        }

        if ( $this->getCGMSolicitante() ) {
            $stFiltro .= " AND req.cgm_solicitante = ".$this->getCGMSolicitante();
        }

        if ( $this->getCGMRequisitante() ) {
            $stFiltro .= " AND req.cgm_requisitante = ".$this->getCGMRequisitante();
        }

        if ($this->stAcao == "homologar") {
            $stFiltro .= " AND ((SELECT homologada FROM almoxarifado.requisicao_homologada rh1                                  \n";
            $stFiltro .= "        WHERE rh1.exercicio = req.exercicio                                                           \n";
            $stFiltro .= "          AND rh1.cod_almoxarifado = req.cod_almoxarifado                                             \n";
            $stFiltro .= "          AND rh1.cod_requisicao = req.cod_requisicao                                                 \n";
            $stFiltro .= "          AND timestamp = (SELECT max(timestamp) FROM almoxarifado.requisicao_homologada rh2          \n";
            $stFiltro .= "                            WHERE rh1.exercicio=rh2.exercicio                                         \n";
            $stFiltro .= "                              AND rh1.cod_almoxarifado = rh2.cod_almoxarifado                         \n";
            $stFiltro .= "                              AND rh1.cod_requisicao = rh2.cod_requisicao)) IS FALSE)                 \n";
        }
        if ($this->stAcao == "anular_homolog") {
            $stFiltro .= " AND ((SELECT homologada FROM almoxarifado.requisicao_homologada rh1                                  \n";
            $stFiltro .= "        WHERE rh1.exercicio = req.exercicio                                                           \n";
            $stFiltro .= "          AND rh1.cod_almoxarifado = req.cod_almoxarifado                                             \n";
            $stFiltro .= "          AND rh1.cod_requisicao = req.cod_requisicao                                                 \n";
            $stFiltro .= "          AND timestamp = (SELECT max(timestamp) FROM almoxarifado.requisicao_homologada rh2          \n";
            $stFiltro .= "                            WHERE rh1.exercicio=rh2.exercicio                                         \n";
            $stFiltro .= "                              AND rh1.cod_almoxarifado = rh2.cod_almoxarifado                         \n";
            $stFiltro .= "                              AND rh1.cod_requisicao = rh2.cod_requisicao)) IS TRUE)                  \n";
        }

        $stOrdem .= " GROUP BY  req.exercicio                               \n";
        $stOrdem .= " 	     ,  req.cod_almoxarifado                        \n";
        $stOrdem .= " 	     ,  req.cod_requisicao                          \n";
        $stOrdem .= " 	     ,  req.cgm_requisitante                        \n";
        $stOrdem .= " 	     ,  req.cgm_solicitante                         \n";
        $stOrdem .= " 	     ,  req.dt_requisicao                           \n";
        $stOrdem .= " 	     ,  req.observacao                              \n";
        $stOrdem .= " 	     ,  cgm.nom_cgm                                 \n";
        $stOrdem .= " 	     ,  cgm.numcgm                                  \n";
        $stOrdem .= " 	     ,  cgm2.nom_cgm                                \n";
        $stOrdem .= " 	     ,  cgm2.numcgm                                 \n";

        if (empty($stOrder)) {
            $stOrdem .= " ORDER BY req.cod_requisicao                       \n";
        } else {
            $stOrdem .= " ORDER BY ".$stOrder;
        }

        $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
        $obTAlmoxarifadoRequisicao->setDado('acao', $this->getAcao());
        $obErro = $obTAlmoxarifadoRequisicao->recuperaRequisicaoAlteracao($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;

    }

    public function listarRequisicaoAlteracaoAnulacao(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicaoAnulacao.class.php"         );
        $this->addRequisicaoItem();

        if ( $this->getCodigo() ) {
            $stFiltro .= " and req.cod_requisicao = ". $this->getCodigo();
        }

        if ( $this->getExercicio() ) {
            $stFiltro .= " and req.exercicio = '".$this->getExercicio()."' ";
        }

        if ( $this->obRAlmoxarifadoAlmoxarifado->getCodigo() ) {
            $stFiltro .= " and req.cod_almoxarifado in (".$this->obRAlmoxarifadoAlmoxarifado->getCodigo().") ";
        }

        if ( $this->getObservacao() ) {
            $stFiltro .= " and req.observacao ilike '".$this->getObservacao()."' ";
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo() ) {
            $stFiltro .= " and req.cod_item = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo();
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo() ) {
            $stFiltro .= " and req.cod_marca = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo();
        }

        if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() ) {
            $stFiltro .= " and req.cod_centro = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo();
        }

        $stOrdem .= " GROUP BY  req.exercicio                               \n";
        $stOrdem .= " 	     ,  req.cod_almoxarifado                        \n";
        $stOrdem .= " 	     ,  req.cod_requisicao                          \n";
        $stOrdem .= " 	     ,  req.cgm_requisitante                        \n";
        $stOrdem .= " 	     ,  req.cgm_solicitante                         \n";
        $stOrdem .= " 	     ,  req.dt_requisicao                           \n";
        $stOrdem .= " 	     ,  req.observacao                              \n";
        $stOrdem .= " 	     ,  cgm.nom_cgm                                 \n";
        $stOrdem .= " 	     ,  cgm.numcgm                                  \n";
        $stOrdem .= " 	     ,  cgm2.nom_cgm                                \n";
        $stOrdem .= " 	     ,  cgm2.numcgm                                 \n";

        if (empty($stOrder)) {
            $stOrdem .= " ORDER BY req.cod_requisicao \n";
        } else {
            $stOrdem .= " ORDER BY ".$stOrder;
        }

        $obTAlmoxarifadoRequisicaoAnulacao = new TAlmoxarifadoRequisicaoAnulacao;
        $obTAlmoxarifadoRequisicaoAnulacao->setDado( 'acao', $this->getAcao() );
        $obErro = $obTAlmoxarifadoRequisicaoAnulacao->recuperaRequisicaoAlteracaoAnulacao($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;

    }

    public function listarPermiteMovimentacao(&$rsRecordSet, $arCodAlmoxarifados, $dtDataInicial, $dtDataFinal, $stTipo = 'S', $stOrder = '', $boTransacao = '')
    {
       $stFiltro = "";
       if ($dtDataInicial) {
            $stFiltro .= " and requisicao.dt_requisicao between TO_DATE('".$dtDataInicial."','dd/mm/yyyy') and TO_DATE('".$dtDataFinal."','dd/mm/yyyy')";
       }
       $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
        if ( count($arCodAlmoxarifados) > 0  ) {
           $stAlmoxarifados = implode(',',$arCodAlmoxarifados );
           $stFiltro .= " and requisicao.cod_almoxarifado in (".$stAlmoxarifados.")";
       }

       if ( $this->getCodigo() ) {
           $stFiltro .= " and requisicao.cod_requisicao = ". $this->getCodigo();
       }

       if ( $this->getExercicio() ) {
           $stFiltro .= " and requisicao.exercicio = '".$this->getExercicio()."' ";
       }

       if ( $this->obRAlmoxarifadoAlmoxarifado->getCodigo() ) {
           $stFiltro .= " and requisicao.cod_almoxarifado = ".$this->obRAlmoxarifadoAlmoxarifado->getCodigo();
       }

       if ( $this->getObservacao() ) {
           $stFiltro .= " and requisicao.observacao ilike '".$this->getObservacao()."' ";
       }

       if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo() ) {
            $stFiltro .= " and requisicao_item.cod_item = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo();
       }

       if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo() ) {
            $stFiltro .= " and requisicao_item.cod_marca = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo();
       }

       if ( $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() ) {
            $stFiltro .= " and requisicao_item.cod_centro = ".$this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo();
       }

       $stOrder = $stOrder != '' ? $stOrder : " order by cod_requisicao ";

       if ($stTipo == 'S') {
          $obErro = $obTAlmoxarifadoRequisicao->recuperaPermiteMovimentacaoSaida($rsRecordSet, $stFiltro, $stOrder, $boTransacao);
       } else {
          $obErro = $obTAlmoxarifadoRequisicao->recuperaPermiteMovimentacaoEntrada($rsRecordSet, $stFiltro, $stOrder, $boTransacao);
       }

       return $obErro;
    }

    public function listar(&$rsRecordSet, $stOrder = '', $boTransacao = '')
    {
        $stFiltro = "";
        if ( $this->getDescricao() ) {
            $stFiltro .= " descricao like '". $this->getDescricao() ."' and ";
        }

        if ( $this->getCodigo() ) {
            $stFiltro .= " arm.cod_requisicao = ". $this->getCodigo()." and ";
        }

        if ( $this->getExercicio() ) {
            $stFiltro .= " arm.exercicio = '".$this->getExercicio()."' and ";
        }

        if ( $this->obRAlmoxarifadoAlmoxarifado->getCodigo() ) {
            $stFiltro .= " arm.cod_almoxarifado = ".$this->obRAlmoxarifadoAlmoxarifado->getCodigo()." and ";
        }

        if ( $this->getObservacao() ) {
            $stFiltro .= " arm.observacao ilike '".$this->getObservacao()."' and ";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
        $obErro = $obTAlmoxarifadoRequisicao->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }

    public function consultar($boTransacao = "")
    {
        $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
        $obTAlmoxarifadoRequisicao->setDado( "cod_requisicao" , $this->getCodigo() );
        $obTAlmoxarifadoRequisicao->setDado( "cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
        $obTAlmoxarifadoRequisicao->setDado( "exercicio", $this->getExercicio());
        $obErro = $obTAlmoxarifadoRequisicao->recuperaPorChave( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() ) {
           $this->obRCGMRequisitante->obRCGM->setNumCGM( $rsRecordSet->getCampo("cgm_requisitante") );
           $this->obRCGMRequisitante->consultar(new RecordSet(), $boTransacao);
           $this->obRCGMSolicitante->setNumCGM( $rsRecordSet->getCampo("cgm_solicitante") );
           $this->obRCGMSolicitante->consultar(new RecordSet(), $boTransacao);
           $this->setObservacao( $rsRecordSet->getCampo("observacao") );
           $this->setDataRequisicao( $rsRecordSet->getCampo("dt_requisicao"));
           $this->obRAlmoxarifadoAlmoxarifado->consultar($boTransacao);
           $this->listarRequisicaoItens($rsRequisicaoItens, $boTransacao);

           while (!$rsRequisicaoItens->eof()) {
              $this->addRequisicaoItem();
              $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($rsRequisicaoItens->getCampo('cod_item'));
              $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($rsRequisicaoItens->getCampo('cod_marca'));
              $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($rsRequisicaoItens->getCampo('cod_centro'));
              $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->setCodigo($rsRequisicaoItens->getCampo('cod_almoxarifado'));
              $this->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRFrotaItem->setCodigo($rsRequisicaoItens->getCampo('cod_item'));

              $obErro = $this->roUltimoRequisicaoItem->consultar($boTransacao);
              $rsRequisicaoItens->proximo();
           }
        }

        return $obErro;
    }

    public function listarRequisicaoItens(&$rsRecordSet, $boTransacao = "")
    {
       $rsRecordSet = new RecordSet;
       $obRAlmxoxarifadoRequisicaoItem = new RAlmoxarifadoRequisicaoItem($this);
       $obRAlmxoxarifadoRequisicaoItem->listar($rsRecordSet, '', '', $boTransacao);
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function incluir($boTransacao="")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
            $obTAlmoxarifadoRequisicao->setDado("cod_almoxarifado",$this->obRAlmoxarifadoAlmoxarifado->getCodigo() );
            $obTAlmoxarifadoRequisicao->setDado("exercicio"       ,$this->getExercicio());
            $obErro =  $obTAlmoxarifadoRequisicao->proximoCod( $inCodigo , $boTransacao );
            $this->setCodigo($inCodigo);
            if ( !$obErro->ocorreu() ) {
                 $obTAlmoxarifadoRequisicao->setDado("cod_requisicao"  ,$this->getCodigo() );
                 $obTAlmoxarifadoRequisicao->setDado("cgm_requisitante",$this->obRCGMRequisitante->obRCGM->getNumCGM());
                 $obTAlmoxarifadoRequisicao->setDado("cgm_solicitante" ,$this->obRCGMSolicitante->getNumCGM());
                 $obTAlmoxarifadoRequisicao->setDado("observacao"      ,$this->getObservacao() );
                 #echo "<pre>";
                 #var_dump($obTAlmoxarifadoRequisicao);
                 #echo "</pre>";
                 $obErro = $obTAlmoxarifadoRequisicao->inclusao( $boTransacao );
                 for ($i=0;$i<count($this->arRAlmoxarifadoRequisicaoItem);$i++) {
                     $obRAlmoxarifadoRequisicaoItem = $this->arRAlmoxarifadoRequisicaoItem[$i];
                     $obRAlmoxarifadoRequisicaoItem->incluir($boTransacao);
                 }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $boHomologaAutomatico = SistemaLegado::pegaConfiguracao('homologacao_automatica_requisicao', 29);
            $boHomologaAutomatico = ($boHomologaAutomatico == 'true') ? true : false;
            include_once( TALM . "TAlmoxarifadoRequisicaoHomologada.class.php"      );
            $obHomologacao = new TAlmoxarifadoRequisicaoHomologada;
            $obHomologacao->setDado( 'cod_requisicao' , 	$this->getCodigo());
            $obHomologacao->setDado( 'cod_almoxarifado', 	$this->obRAlmoxarifadoAlmoxarifado->getCodigo());
            $obHomologacao->setDado( 'exercicio', 			$this->getExercicio() );
            $obHomologacao->setDado( 'cgm_homologador',   	Sessao::read('numCgm'));
            $obHomologacao->setDado( 'homologada',          $boHomologaAutomatico);

            $obErro = $obHomologacao->inclusao($boTransacao);
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoRequisicao);

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function alterar($boTransacao="")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();
            if ( !$obErro->ocorreu() ) {
                 $obTAlmoxarifadoRequisicao->setDado("cod_requisicao"      , $this->getCodigo() );
                 $obTAlmoxarifadoRequisicao->setDado("cod_almoxarifado"    , $this->obRAlmoxarifadoAlmoxarifado->getCodigo() );
                 $obTAlmoxarifadoRequisicao->setDado("exercicio"           , $this->getExercicio());
                 $obTAlmoxarifadoRequisicao->setDado("cgm_requisitante"    , $this->obRCGMRequisitante->obRCGM->getNumCGM());
                 $obTAlmoxarifadoRequisicao->setDado("cgm_solicitante"     , $this->obRCGMSolicitante->getNumCGM());
                 $obTAlmoxarifadoRequisicao->setDado("observacao"          , $this->getObservacao());
                 $obErro = $obTAlmoxarifadoRequisicao->alteracao( $boTransacao );
                 $obRAlmoxarifadoRequisicaoItem = new RAlmoxarifadoRequisicaoItem($this);
                 $obRAlmoxarifadoRequisicaoItem->excluir($boTransacao);
                 for ($i=0;$i<count($this->arRAlmoxarifadoRequisicaoItem);$i++) {
                     $obRAlmoxarifadoRequisicaoItem = $this->arRAlmoxarifadoRequisicaoItem[$i];
                     $obRAlmoxarifadoRequisicaoItem->incluir($boTransacao);
                 }
            }
        }
        /* Alteração está indo pelo PR.
        if ( !$obErro->ocorreu() ) {
            $boHomologaAutomatico = SistemaLegado::pegaConfiguracao('homologacao_automatica_requisicao', 29);
            $boHomologaAutomatico = ($boHomologaAutomatico == 'true') ? true : false;
            include_once( TALM . "TAlmoxarifadoRequisicaoHomologada.class.php"      );
            $obHomologacao = new TAlmoxarifadoRequisicaoHomologada;
            $obHomologacao->setDado( 'cod_requisicao' , 	$this->getCodigo());
            $obHomologacao->setDado( 'cod_almoxarifado', 	$this->obRAlmoxarifadoAlmoxarifado->getCodigo());
            $obHomologacao->setDado( 'exercicio', 			$this->getExercicio() );
            $obHomologacao->setDado( 'cgm_homologador',   	Sessao::read('numCgm'));
            $obHomologacao->setDado( 'homologada',          $boHomologaAutomatico);

            $obErro = $obHomologacao->inclusao($boTransacao);
        }
        */
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoRequisicao);

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function excluir($boTransacao="")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoRequisicao = new TAlmoxarifadoRequisicao();

            for ($i=0;$i<count($this->arRAlmoxarifadoRequisicaoItem);$i++) {
                $obRAlmoxarifadoRequisicaoItem = $this->arRAlmoxarifadoRequisicaoItem[$i];
                $obRAlmoxarifadoRequisicaoItem->excluir($boTransacao);
            }

            $obTAlmoxarifadoRequisicao->setDado( "cod_requisicao"         , $this->getCodigo()           );
            $obTAlmoxarifadoRequisicao->setDado( "exercicio"              , $this->getExercicio()        );
            $obTAlmoxarifadoRequisicao->setDado( "cod_almoxarifado"       , $this->obRAlmoxarifadoAlmoxarifado->getCodigo() );

            $obErro = $obTAlmoxarifadoRequisicao->exclusao( $boTransacao );
       }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoRequisicao);

        return $obErro;
    }

}

?>
