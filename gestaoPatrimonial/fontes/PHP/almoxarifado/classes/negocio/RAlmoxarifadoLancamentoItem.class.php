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

/*
$Log$
Revision 1.6  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:09:31  diego

*/

include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php"         );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterialValor.class.php"    );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoDoacaoEmprestimo.class.php" );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoPerecivel.class.php" );
//include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicaoAnulacao.class.php"         );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php"                   );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                                       );

/**
    * Classe de Regra de Classificação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoLancamentoItem
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
    public $obRAlmoxarifadoEstoqueItem;

    /**
        * @access Private
        * @var Object
    */
    public $obRCGMDoador;

    /**
        * @access Private
        * @var Integer
    */

    public $inCodigo;

    /**
        * @access Private
        * @var Numeric
    */

    public $nmQuantidade;

    /**
        * @access Private
        * @var String
    */

    public $stComplemento;

    /**
        * @access Private
        * @var Numeric
    */

    public $nmValor;

    /**
         * @access Public
         * @return Integer
     */

   public function setCodigo($valor) { $this->inCodigo = $valor; }

    /**
         * @access Public
         * @return Numeric
     */

   public function setQuantidade($valor) { $this->nmQuantidade = $valor; }

    /**
         * @access Public
         * @return String
     */

   public function setComplemento($valor) { $this->stComplemento = $valor; }

    /**
         * @access Public
         * @return Numeric
     */

   public function setValor($valor) { $this->nmValor = $valor; }

    /**
         * @access Public
         * @return Integer
     */

    public function getCodigo() { return $this->inCodigo; }

    /**
         * @access Public
         * @return Numeric
     */

    public function getQuantidade() { return $this->nmQuantidade; }

    /**
         * @access Public
         * @return Integer
     */

    public function getComplemento() { return $this->stComplemento; }

    /**
         * @access Public
         * @return Numeric
     */

    public function getValor() { return $this->nmValor; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoLancamentoItem(&$obRAlmoxarifadoNaturezaLancamento)
    {
        $this->obRAlmoxarifadoEstoqueItem = new RAlmoxarifadoEstoqueItem();
        $this->obRCGMDoador = new RCGM();
        $this->roAlmoxarifadoLancamento = &$obRAlmoxarifadoNaturezaLancamento;
        $this->obTransacao  = new Transacao ;
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
            $obTAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial();
            $obErro =  $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodigo , $boTransacao );
            $this->setCodigo($inCodigo);
            if ( !$obErro->ocorreu() ) {
               $obTAlmoxarifadoLancamentoMaterial->setDado("cod_lancamento"      , $this->getCodigo() );
               $obTAlmoxarifadoLancamentoMaterial->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
               $obTAlmoxarifadoLancamentoMaterial->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
               $obTAlmoxarifadoLancamentoMaterial->setDado("cod_almoxarifado"    , $this->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo() );
               $obTAlmoxarifadoLancamentoMaterial->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
               $obTAlmoxarifadoLancamentoMaterial->setDado("exercicio_lancamento", $this->roAlmoxarifadoLancamento->getExercicio());
               $obTAlmoxarifadoLancamentoMaterial->setDado("num_lancamento",       $this->roAlmoxarifadoLancamento->getNumero());
               $obTAlmoxarifadoLancamentoMaterial->setDado("cod_natureza",         $this->roAlmoxarifadoLancamento->obRAlmoxarifadoNatureza->getCodigo());
               $obTAlmoxarifadoLancamentoMaterial->setDado("tipo_natureza",        $this->roAlmoxarifadoLancamento->obRAlmoxarifadoNatureza->getTipo());
               $obTAlmoxarifadoLancamentoMaterial->setDado("quantidade"          , $this->getQuantidade() );
               $obTAlmoxarifadoLancamentoMaterial->setDado("complemento"         , $this->getComplemento() );
               $obErro = $obTAlmoxarifadoLancamentoMaterial->inclusao( $boTransacao );
               if ( !$obErro->ocorreu() ) {
                   if ( $this->getValor() ) {
                        $obTAlmoxarifadoLancamentoMaterialValor = new TAlmoxarifadoLancamentoMaterialValor();
                        $obTAlmoxarifadoLancamentoMaterialValor->setDado("cod_lancamento"      , $this->getCodigo() );
                        $obTAlmoxarifadoLancamentoMaterialValor->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
                        $obTAlmoxarifadoLancamentoMaterialValor->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
                        $obTAlmoxarifadoLancamentoMaterialValor->setDado("cod_almoxarifado"    , $this->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo() );
                        $obTAlmoxarifadoLancamentoMaterialValor->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
                        $obTAlmoxarifadoLancamentoMaterialValor->setDado("valor_mercado"       , $this->getValor() );
                        $obErro = $obTAlmoxarifadoLancamentoMaterialValor->inclusao( $boTransacao );
                   }
                   if (!$obErro->ocorreu() ) {
                       if ($this->obRCGMDoador->getNumCGM()) {
                          $obTAlmoxarifadoLancamentoDoacaoEmprestimo = new TAlmoxarifadoLancamentoDoacaoEmprestimo();
                          $obTAlmoxarifadoLancamentoDoacaoEmprestimo->setDado("cod_lancamento"      , $this->getCodigo() );
                          $obTAlmoxarifadoLancamentoDoacaoEmprestimo->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
                          $obTAlmoxarifadoLancamentoDoacaoEmprestimo->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
                          $obTAlmoxarifadoLancamentoDoacaoEmprestimo->setDado("cod_almoxarifado"    , $this->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo() );
                          $obTAlmoxarifadoLancamentoDoacaoEmprestimo->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
                          $obTAlmoxarifadoLancamentoDoacaoEmprestimo->setDado("numcgm"              , $this->obRCGMDoador->getNumCGM() );
                          $obErro = $obTAlmoxarifadoLancamentoDoacaoEmprestimo->inclusao( $boTransacao );
                       }
                       if (!$obErro->ocorreu()) {
                           if (!empty($this->obRAlmoxarifadoEstoqueItem->arRAlmoxarifadoPerecivel)) {
                               $obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
                               for ($i = 0; $i <=count($this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->arRAlmoxarifadoPerecivel[$i]); $i++) {
                                   $obTAlmoxarifadoLancamentoPerecivel->setDado("cod_lancamento"      , $this->getCodigo() );
                                   $obTAlmoxarifadoLancamentoPerecivel->setDado("lote"                , $this->obRAlmoxarifadoEstoqueItem->arRAlmoxarifadoPerecivel[$i]->getLote());
                                   $obTAlmoxarifadoLancamentoPerecivel->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo());
                                   $obTAlmoxarifadoLancamentoPerecivel->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo());
                                   $obTAlmoxarifadoLancamentoPerecivel->setDado("cod_almoxarifado"    , $this->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->getCodigo() );
                                   $obTAlmoxarifadoLancamentoPerecivel->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo() );
                                   $obErro = $obTAlmoxarifadoLancamentoPerecivel->inclusao( $boTransacao );
                               }
                           }
                       }
                   }
               }
           }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoLancamentoMaterial);

        return $obErro;
    }

}
