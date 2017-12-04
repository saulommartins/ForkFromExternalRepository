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
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Regra

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.12
*/

/*
$Log$
Revision 1.15  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.14  2006/07/06 12:09:31  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                             );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogo.class.php"                                 );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php"                    );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php"                             );

/**
    * Classe de Regra de Importação de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista
*/

class RAlmoxarifadoImportacaoCatalogo
{
/**
      * @access Private
      * @var RecordSet
*/
    public $rsCatalogo;

/**
      * @access Private
      * @var RecordSet
*/
    public $rsClassificacao;

/**
      * @access Private
      * @var RecordSet
*/
    public $rsItem;

/**
      * @access Private
      * @var RecordSet
*/
    public $rsAtributo;

/**
      * @access Private
      * @var RecordSet
*/
    public $rsItemAtributo;

/**
      * @access Private
      * @var Object
*/
    public $obRAtributoDinamico ;

//setters
/**
     * @access Public
     * @param String $valor
*/

    public function setRecordSetCatalogo($rsCatalogo) {$this->rsCatalogo = $rsCatalogo;}

/**
     * @access Public
     * @param String $valor
*/

    public function setRecordSetClassificacao($rsClassificacao) {$this->rsClassificacao = $rsClassificacao;}

/**
     * @access Public
     * @param String $valor
*/

    public function setRecordSetItem($rsItem) {$this->rsItem = $rsItem;}

/**
     * @access Public
     * @param String $valor
*/

    public function setRecordSetAtributo($rsAtributo) {$this->rsAtributo = $rsAtributo;}

/**
     * @access Public
     * @param String $valor
*/

    public function setRecordSetItemAtributo($rsItemAtributo) {$this->rsItemAtributo = $rsItemAtributo;}

//Getters

  /**
        * @access Public
        * @return RecordSet
    */

    public function getRecordSetCatalogo() { return $this->rsCatalogo; }

  /**
        * @access Public
        * @return RecordSet
    */

    public function getRecordSetClassificacao() { return $this->rsClassificacao; }

  /**
        * @access Public
        * @return RecordSet
    */

    public function getRecordSetItem() { return $this->rsItem; }

  /**
        * @access Public
        * @return RecordSet
    */

    public function getRecordSetAtributo() { return $this->rsAtributo; }

  /**
        * @access Public
        * @return RecordSet
    */

    public function getRecordSetItemAtributo() { return $this->rsItemAtributo; }

    public function RAlmoxarifadoImportacaoCatalogo()
    {
         $this->obTransacao  = new Transacao ;
         $this->obRAlmoxarifadoCatalogo = new RAlmoxarifadoCatalogo();
         $this->obRAlmoxarifadoClassificacao = new RAlmoxarifadoCatalogoClassificacao();
         $this->obRAlmoxarifadoItem = new RAlmoxarifadoCatalogoItem();
    }

    public function incluir($boTransacao = '')
    {
        $boFlagTransacao = false;

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $this->rsCatalogo->setPrimeiroElemento();
        $this->rsClassificacao->setPrimeiroElemento();
        $this->rsItem->setPrimeiroElemento();
        if ($this->rsAtributo) {
            $this->rsAtributo->setPrimeiroElemento();
            $this->rsItemAtributo->setPrimeiroElemento();
        }
        if ( !$obErro->ocorreu() ) {

            $this->obRAlmoxarifadoCatalogo->setDescricao( $this->rsCatalogo->getCampo('Descrição do Catálogo') );

            while (!$this->rsCatalogo->eof()) {
                $this->obRAlmoxarifadoCatalogo->addCatalogoNivel();
                $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->setMascara($this->rsCatalogo->getCampo('Máscara do Nível'));
                $this->obRAlmoxarifadoCatalogo->roCatalogoNivel->setDescricao($this->rsCatalogo->getCampo('Descrição do Nível'));
                $this->rsCatalogo->proximo();
            }
            $obErro = $this->obRAlmoxarifadoCatalogo->incluir($boTransacao);
            if (!$obErro->ocorreu()) {
                if ($this->rsAtributo) {
                    while (!$this->rsAtributo->eof()) {
                        $this->obRAtributoDinamico = new RAtributoDinamico();
                        $this->obRAtributoDinamico->obRModulo->setCodModulo( 29 );
                        $this->obRAtributoDinamico->setCodCadastro   ( 1 );
                        $this->obRAtributoDinamico->setIndexavel('false');
                        $this->obRAtributoDinamico->setAtivo('true');
                        $this->obRAtributoDinamico->setCodTipo($this->rsAtributo->getCampo('Código do Tipo do Atributo'));
                        $this->obRAtributoDinamico->setObrigatorio($this->rsAtributo->getCampo('Atributo pode ser Nulo'));
                        $this->obRAtributoDinamico->setNome($this->rsAtributo->getCampo('Nome do Atributo'));
                        $this->obRAtributoDinamico->setMascara($this->rsAtributo->getCampo('Máscara'));
                        $this->obRAtributoDinamico->setAjuda($this->rsAtributo->getCampo('Ajuda do Atributo'));
                        $this->obRAtributoDinamico->addValor ( 1, 'Sim','' );
                        $obErro = $this->obRAtributoDinamico->salvar($boTransacao);
                        $arCodigoAtributo[$this->rsAtributo->getCampo('Nome do Atributo')] = $this->obRAtributoDinamico->getCodAtributo();
                        if ($obErro->ocorreu()) {
                            $obErro->setDescricao($obErro->getDescricao());
                            break;
                        }
                        $count++;
                        $this->rsAtributo->proximo();
                    }
                    while (!$this->rsItemAtributo->eof()) {
                        $this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->addAtributosDinamicos($arCodigoAtributo[$this->rsItemAtributo->getCampo('Nome do Atributo')]);
                        $this->rsItemAtributo->proximo();
                    }
                    $this->rsItemAtributo->setPrimeiroElemento();
                }
                if (!$obErro->ocorreu()) {
                    $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo($this->obRAlmoxarifadoCatalogo->getCodigo());
                    while (!$this->rsClassificacao->eof()) {
                        $this->obRAlmoxarifadoClassificacao->setEstrutural($this->rsClassificacao->getCampo('Código Estrutural'));
                        $this->obRAlmoxarifadoClassificacao->setDescricao($this->rsClassificacao->getCampo('Descrição da Classificação'));
                        $obErro =  $this->obRAlmoxarifadoClassificacao->incluirClassificacao($boTransacao);
                        if (!$obErro->ocorreu()) {
                            $this->rsClassificacao->proximo();
                        } else {
                            break;
                        }
                    }
                    if (!$obErro->ocorreu()) {
                        while (!$this->rsItem->eof()) {
                            $rsClassificacao = new RecordSet;
                            $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo($this->obRAlmoxarifadoCatalogo->getCodigo());
                            $this->obRAlmoxarifadoClassificacao->setEstrutural($this->rsItem->getCampo('Código Estrutural'));
                            $obErro = $this->obRAlmoxarifadoClassificacao->recuperaCodigoClassificacao($rsClassificacao,$boTransacao);
                            $stCodigos =  strval($this->rsItem->getCampo('Código da Unidade de Medida'));
                            if ( $obErro->ocorreu() ) {
                                    $obErro->setDescricao("Verifique seu arquivo de classificação");
                                break;
                            }
                            $this->obRAlmoxarifadoItem = new RAlmoxarifadoCatalogoItem();
                            $this->obRAlmoxarifadoItem->obRAlmoxarifadoClassificacao->setCodigo($rsClassificacao->getCampo('cod_classificacao'));
                            $this->obRAlmoxarifadoItem->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo($this->obRAlmoxarifadoCatalogo->getCodigo() );
                            $this->obRAlmoxarifadoItem->setCodigo($this->rsItem->getCampo('Código do Item'));
                            $this->obRAlmoxarifadoItem->obRUnidadeMedida->setCodUnidade($stCodigos[1]);
                            $this->obRAlmoxarifadoItem->obRUnidadeMedida->obRGrandeza->setCodGrandeza($stCodigos[0]);
                            $this->obRAlmoxarifadoItem->obRAlmoxarifadoTipoItem->setCodigo($this->rsItem->getCampo('Código do Tipo de Item'));
                            $this->obRAlmoxarifadoItem->setDescricao($this->rsItem->getCampo('Descrição do Item'));
                            $this->obRAlmoxarifadoItem->obRAlmoxarifadoControleEstoque->setEstoqueMinimo($this->rsItem->getCampo('Estoque Mínimo'));
                            $this->obRAlmoxarifadoItem->obRAlmoxarifadoControleEstoque->setEstoqueMaximo($this->rsItem->getCampo('Estoque Máximo'));
                            $this->obRAlmoxarifadoItem->obRAlmoxarifadoControleEstoque->setPontoDePedido($this->rsItem->getCampo('Ponto de Pedido'));
                            if ($this->rsAtributo) {
                                while (!$this->rsItemAtributo->eof()) {
                                    if ($this->rsItem->getCampo('Código do Item') == $this->rsItemAtributo->getCampo('Código do Item')) {
                                        $this->obRAlmoxarifadoItem->obRAlmoxarifadoClassificacao->obRCadastroDinamico->addAtributosDinamicos( $arCodigoAtributo[$this->rsItemAtributo->getCampo('Nome do Atributo')],$this->rsItemAtributo->getCampo('Valor do Atributo'));
                                    }
                                    $this->rsItemAtributo->proximo();
                                }
                                $this->rsItemAtributo->setPrimeiroElemento();
                            }
                            $obErro = $this->obRAlmoxarifadoItem->incluir($boTransacao);
                            if (!$obErro->ocorreu()) {
                                $this->rsItem->proximo();
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro /**/ );
        }

        return $obErro;
    }

}
