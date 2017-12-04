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
    * Classe de Regra de Catálogo
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.07 uc-03.03.06
*/

/*
$Log$
Revision 1.11  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:09:31  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );
include_once ( CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoControleEstoque.class.php" );

/**
    * Classe de Regra de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/

class RAlmoxarifadoControleEstoque
{

    /**
        * @access Private
        * @var Integer
    */

    public $inEstoqueMinimo;

    /**
        * @access Private
        * @var Integer
    */

    public $inEstoqueMaximo;

    /**
        * @access Private
        * @var Integer
    */

    public $inPontoDePedido;

    /**
        * @access Private
        * @var String
    */

    public $stCodigoBarras;

    /**
        * @access Public
        * @return Integer
    */

    public function setEstoqueMinimo($valor) { $this->inEstoqueMinimo = $valor; }

    /**
        * @access Public
        * @return Integer
    */

    public function setEstoqueMaximo($valor) { $this->inEstoqueMaximo = $valor; }

    /**
        * @access Public
        * @return Integer
    */

    public function setPontoDePedido($valor) { $this->inPontoDePedido = $valor; }

    /**
        * @access Public
        * @return Integer
    */

    public function setCodigoBarras($valor) { $this->stCodigoBarras = $valor; }

    /**
        * @access Public
        * @return Integer
    */

    public function getEstoqueMinimo() { return $this->inEstoqueMinimo; }

    /**
        * @access Public
        * @return Integer
    */

    public function getEstoqueMaximo() { return $this->inEstoqueMaximo; }
   /**
        * @access Public
        * @return Integer
    */

    public function getPontoDePedido() { return $this->inPontoDePedido; }

    /**
        * @access Public
        * @return String
    */

    public function getCodigoBarras() { return $this->stCodigoBarras; }

    /**
         * Método construtor
         * @access Public
    */
    public function RAlmoxarifadoControleEstoque(&$obRAlmoxarifadoCatalogoItem)
    {
        $this->roAlmoxarifadoCatalogoItem = &$obRAlmoxarifadoCatalogoItem;
        $this->obTransacao  = new Transacao ;
    }

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
    {
        $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque();
        $obErro = $obTAlmoxarifadoControleEstoque->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaPorChave na classe Persistente
        * @access Public
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function consultar($boTransacao = "")
    {
        $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque;
        $obTAlmoxarifadoControleEstoque->setDado("cod_item", $this->roAlmoxarifadoCatalogoItem->getCodigo());
        $obErro = $obTAlmoxarifadoControleEstoque->recuperaPorChave( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->setEstoqueMinimo(number_format((float) $rsRecordSet->getCampo("estoque_minimo"), 4, ',', '.'));
            $this->setEstoqueMaximo(number_format((float) $rsRecordSet->getCampo("estoque_maximo"), 4, ',', '.'));
            $this->setPontoDePedido(number_format((float) $rsRecordSet->getCampo("ponto_pedido")  , 4, ',', '.'));
            $this->setCodigoBarras ($rsRecordSet->getCampo("codigo_barras"));

        }

        return $obErro;
    }

    /**
        * Incluir Catalogo
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function incluir($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque;
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->valida();
            if ( !$obErro->ocorreu() ) {
                $obTAlmoxarifadoControleEstoque->setDado( "cod_item" , $this->roAlmoxarifadoCatalogoItem->getCodigo());
                $obTAlmoxarifadoControleEstoque->setDado( "estoque_minimo", $this->inEstoqueMinimo);
                $obTAlmoxarifadoControleEstoque->setDado( "estoque_maximo", $this->inEstoqueMaximo);
                $obTAlmoxarifadoControleEstoque->setDado( "ponto_pedido"  , $this->inPontoDePedido);
                $obTAlmoxarifadoControleEstoque->setDado( "codigo_barras" , $this->stCodigoBarras );

                $obErro = $obTAlmoxarifadoControleEstoque->inclusao( $boTransacao );

            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoControleEstoque);
        }

        return $obErro;
    }

    /**
        * Alterar Controle de Estoque
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function alterar($boTransacao = "")
    {
        $boIncluir = false;
        $boFlagTransacao = false;
        $rsNiveis = new RecordSet();
        $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque;

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->valida();
            if ( !$obErro->ocorreu() ) {

               $obTAlmoxarifadoControleEstoque->setDado( "cod_item" , $this->roAlmoxarifadoCatalogoItem->getCodigo()  );
               $obTAlmoxarifadoControleEstoque->setDado( "estoque_minimo", $this->getEstoqueMinimo()         );
               $obTAlmoxarifadoControleEstoque->setDado( "estoque_maximo", $this->getEstoqueMaximo()         );
               $obTAlmoxarifadoControleEstoque->setDado( "ponto_pedido", $this->getPontoDePedido()  );
               $obTAlmoxarifadoControleEstoque->setDado( "codigo_barras", $this->getCodigoBarras() );

               $obErro = $obTAlmoxarifadoControleEstoque->alteracao( $boTransacao );

               $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoControleEstoque );
            }
        }

        return $obErro;
    }

    /**
        * Exclui Controle de Estoque
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function excluir($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque();

        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoControleEstoque->setDado( "cod_item", $this->roAlmoxarifadoCatalogoItem->getCodigo() );

            $obErro = $obTAlmoxarifadoControleEstoque->exclusao( $boTransacao );
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoControleEstoque);

        return $obErro;
    }

    public function valida()
    {
        $obErro = new Erro;
        $this->inPontoDePedido = str_replace('.', '', $this->getPontoDePedido());
        $this->inPontoDePedido = str_replace(',', '.', $this->getPontoDePedido());
        $nmPontoDePedido = (float) $this->getPontoDePedido();
        $this->inEstoqueMinimo = str_replace('.', '', $this->getEstoqueMinimo());
        $this->inEstoqueMinimo = str_replace(',', '.', $this->getEstoqueMinimo());
        $nmEstoqueMinimo = (float) $this->getEstoqueMinimo();
        $this->inEstoqueMaximo = str_replace('.', '', $this->getEstoqueMaximo());
        $this->inEstoqueMaximo = str_replace(',', '.', $this->getEstoqueMaximo());
        $nmEstoqueMaximo = (float) $this->getEstoqueMaximo();
        if ($nmEstoqueMinimo < 0) {
           $obErro->setDescricao("O estoque mínimo não pode ser negativo.");
        }
        if ($nmPontoDePedido< 0) {
           $obErro->setDescricao("O ponto de pedido não pode ser negativo.");
        }
        if ($nmEstoqueMaximo< 0) {
           $obErro->setDescricao("O estoque máximo não pode ser negativo.");
        }
        if ($nmEstoqueMaximo < $nmEstoqueMinimo) {
             $obErro->setDescricao("O estoque máximo tem que ser maior ou igual ao estoque mínimo.");
        } elseif ($nmPontoDePedido) {
            if ($nmPontoDePedido < $nmEstoqueMinimo) {
                $obErro->setDescricao("O ponto de pedido tem que ser maior ou igual ao estoque mínimo.");
            } elseif ($nmPontoDePedido > $nmEstoqueMaximo) {
                $obErro->setDescricao("O ponto de pedido tem que ser menor ou igual ao estoque máximo.");
            }
        }

        return $obErro;
    }
}
