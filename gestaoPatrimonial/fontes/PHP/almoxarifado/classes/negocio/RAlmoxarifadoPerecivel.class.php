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
    * Classe de Regra de Perecível
    * Data de Criação   : 18/11/2005

    * @author Analista: Diego Victoria Barbosa
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.11
*/

/*
$Log$
Revision 1.7  2007/08/27 12:54:46  hboaventura
Bug#9996#

Revision 1.6  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:09:32  diego

*/

include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPerecivel.class.php"                 );

/**
    * Classe de Regra de Classificação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoPerecivel
{

    /**
        * @access Private
        * @var Object
    */
    public $obTransacao;

   /**
       * @access Private
       * @var String
   */
   public $stLote;

   /**
       * @access Private
       * @var Date
   */
   public $dtDataFabricacao;

   /**
       * @access Private
       * @var Date
   */
   public $dtDataValidade;

   /**
       * @access Private
       * @var Object
   */
   public $roAlmoxarifadoEstoqueItem;

    /**
         * @access Public
         * @param String
     */
   public function setLote($valor) { $this->stLote = $valor; }

    /**
         * @access Public
         * @param Date
     */
   public function setDataFabricacao($valor) { $this->dtDataFabricacao = $valor; }

    /**
         * @access Public
         * @param Date
     */
   public function setDataValidade($valor) { $this->dtDataValidade = $valor; }

    /**
         * @access Public
         * @return String
     */

    public function getLote() { return $this->stLote; }

    /**
         * @access Public
         * @return Date
     */

    public function getDataFabricacao() { return $this->dtDataFabricacao; }

    /**
         * @access Public
         * @return Date
     */

    public function getDataValidade() { return $this->dtDataValidade; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoPerecivel(&$obRAlmoxarifadoEstoqueItem)
    {
        $this->obTransacao  = new Transacao ;
        $this->roAlmoxarifadoEstoqueItem = &$obRAlmoxarifadoEstoqueItem;
    }

    public function listar(&$rsRecordSet, $stOrder = '', $boTransacao = '')
    {
        $stFiltro = "";
        if ($this->roAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo()) {
             $stFiltro .= " where cod_item = ". $this->roAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo();
        }
        if ($this->roAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo()) {
             $stFiltro .= " and cod_almoxarifado = ". $this->roAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo();
        }
        if ($this->roAlmoxarifadoEstoqueItem->obRMarca->getCodigo()) {
             $stFiltro .= " and cod_marca = ". $this->roAlmoxarifadoEstoqueItem->obRMarca->getCodigo();
        }
        if ($this->roAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo()) {
             $stFiltro .= " and cod_centro = ". $this->roAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo();
        }
        $stOrder = ($stOrder) ? $stOrder : ' ORDER BY dt_validade ASC ';
        $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel();
        $obErro = $obTAlmoxarifadoPerecivel->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

        return $obErro;
    }

    public function consultar($boTransacao = "")
    {
        $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel();
        $obTAlmoxarifadoPerecivel->setDado( "lote" , $this->getLote() );
        $obTAlmoxarifadoPerecivel->setDado( "cod_almoxarifado", $this->roAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado( "cod_item" , $this->roAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado( "cod_marca", $this->roAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado( "cod_centro", $this->roAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obErro = $obTAlmoxarifadoPerecivel->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $this->setDataValidade( $rsRecordSet->getCampo("dt_validade") );
           $this->setDataFabricacao( $rsRecordSet->getCampo("dt_fabricacao") );
        }

        return $obErro;
    }

    /**
      * Retorna o Saldo do Lote
      * @acces Public
      * @param Numeric $inSaldo
      * @return Object $obErro
    */
    public function retornaSaldoLote(&$inSaldo, $obTransacao = "")
    {
        $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
        $stFiltro = "";
        $obTAlmoxarifadoPerecivel->setDado('cod_item', $this->roAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado('cod_marca', $this->roAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado('cod_centro', $this->roAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $this->roAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado('lote', $this->getLote());
        $obErro = $obTAlmoxarifadoPerecivel->recuperaSaldoLote( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );
        $inSaldo = $rsRecordSet->getCampo( 'saldo_lote' );

        return $obErro;
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
            $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel();
            $obTAlmoxarifadoPerecivel->setDado("cod_almoxarifado"    , $this->roAlmoxarifadoEstoqueItem->obRAlmoxarifadoAlmoxarifado->getCodigo() );
            $obTAlmoxarifadoPerecivel->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
            $obTAlmoxarifadoPerecivel->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
            $obTAlmoxarifadoPerecivel->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
            $obTAlmoxarifadoPerecivel->setDado("lote"                 , $this->getLote() );
            $obTAlmoxarifadoPerecivel->setDado("dt_validade"          , $this->getDataValidade() );
            $obTAlmoxarifadoPerecivel->setDado("dt_fabricacao"        , $this->getDataFabricacao() );
            $obErro = $obTAlmoxarifadoPerecivel->inclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoPerecivel);

        return $obErro;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function excluir($boTransacao="")
    {
        $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel();
        $obTAlmoxarifadoPerecivel->setDado( "cod_almoxarifado"       , $this->roAlmoxarifadoEstoqueItem->obRAlmoxarifadoAlmoxarifado->getCodigo() );
        $obTAlmoxarifadoPerecivel->setDado( "cod_item"               , $this->roAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado( "cod_centro"             , $this->roAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado( "cod_marca"              , $this->roAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
        $obTAlmoxarifadoPerecivel->setDado( "lote"              , $this->getLote());
        $obErro = $obTAlmoxarifadoPerecivel->exclusao( $boTransacao );

        return $obErro;
    }

}
