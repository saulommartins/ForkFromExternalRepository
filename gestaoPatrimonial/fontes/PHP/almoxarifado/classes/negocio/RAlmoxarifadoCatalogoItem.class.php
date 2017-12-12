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
    * Data de Criação   : 05/12/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.06
                    uc-03.03.16
                    uc-03.03.17

    $Id: RAlmoxarifadoCatalogoItem.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                                 );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoControleEstoque.class.php");
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoTipoItem.class.php");
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php");
include_once ( CAM_GA_ADM_NEGOCIO."RUnidadeMedida.class.php");
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoClassificacaoItemValor.class.php" );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php" );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoClassificacao.class.php" );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );

/**
    * Classe de Regra de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
*/

class RAlmoxarifadoCatalogoItem
{

    /**
        * @access Private
        * @var Integer
    */

    public $inCodigo;

    /**
        * @access Private
        * @var String
    */

    public $stDescricao;

    /**
        * @access Private
        * @var String
    */

    public $stDescricaoResumida;

    /**
        * @access Private
        * @var Boolean
    */

    public $boAtivo;

    /**
        * @access Private
        * @var Object
    */
    public $obRAlmoxarifadoTipoItem;

    /**
        * @access Private
        * @var Object
    */
    public $obRCadastroDinamico;

    /**
       * @acess Private
       * @var Object
    */
    public $obRUnidadeMedida;

    /**
       * @access Private
       * @var Boolean
    */
    public $boServico;

    /**
       * @access Private
       * @var Boolean
    */
    public $boUnidadeNaoInformado;

    /**
       * @access Private
       * @var Boolean
    */
    public $boTipoNaoInformado;

    /**
       * @access Private
       * @var Boolean
    */
    public $boVerificaSaldo;

    /**
       * @access Private
       * @var Integer
    */
    public $inCodigoAlmoxarifado;

    /**
       * @access Private
       * @var Boolean
    */
    public $boSomenteComMovimentacao;

    /**
        * @access Public
        * @return Integer
    */
    public function setCodigoAlmoxarifado($inCodigoAlmoxarifado) { $this->inCodigoAlmoxarifado = $inCodigoAlmoxarifado; }

    /**
        * @access Public
    */
    public function setVerificarMovimentacaoItem($verificaMovimentacaoItem) { $this->boVerificaMovimentacaoItem = $verificaMovimentacaoItem;}

    /**
        * @access Public
        * @return Integer
    */
    public function setCodigo($inCodigo) { $this->inCodigo = $inCodigo; }

    /**
        * @access Public
        * @return Integer
    */

    public function setDescricao($stDescricao) { $this->stDescricao = $stDescricao; }

    /**
        * @access Public
        * @return Integer
    */

    public function setDescricaoResumida($valor) { $this->stDescricaoResumida = $valor; }

    /**
        * @access Public
        * @return Boolean
    */

    public function setAtivo($valor) { $this->boAtivo = $valor; }

    /**
        * @access Public
        * @return Integer
    */

    public function setServico($valor) { $this->boServico        = $valor;       }

    /**
        * @access Public
        * @return Integer
    */

    public function setUnidadeNaoInformado($valor) { $this->boUnidadeNaoInformado   = $valor;       }

    /**
        * @access Public
        * @return Integer
    */

    public function setTipoNaoInformado($valor) { $this->boTipoNaoInformado   = $valor;       }

    /**
        * @access Public
        * @return Integer
    */
    public function setVerificaSaldo($valor) { $this->boVerificaSaldo = $valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getCodigoAlmoxarifado() { return $this->inCodigoAlmoxarifado; }

    /**
        * @access Public
        * @return Integer
    */

    public function getCodigo() { return $this->inCodigo; }

    /**
        * @access Public
        * @return String
    */

    public function getDescricao() { return $this->stDescricao; }

    /**
        * @access Public
        * @return String
    */

    public function getDescricaoResumida() { return $this->stDescricaoResumida; }

    /**
        * @access Public
        * @return Boolean
    */

    public function getAtivo() { return $this->boAtivo; }

    /**
        * @access Public
        * @return String
    */

    public function getServico() { return $this->boServico;   }

    /**
        * @access Public
        * @return String
    */

    public function getUnidadeNaoInformado() { return $this->boUnidadeNaoInformado;   }

    /**
        * @access Public
        * @return String
    */

    public function getTipoNaoInformado() { return $this->boTipoNaoInformado;   }

    /**
        * @access Public
        * @return String
    */
    public function getVerificaSaldo() { return $this->boVerificaSaldo; }

       /**
        * @access Public
        * @return String
    */
    public function getVerificarMovimentacaoItem() { return $this->boVerificaMovimentacaoItem; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoCatalogoItem()
    {
        $this->obTransacao  = new Transacao ;
        $this->obRAlmoxarifadoTipoItem = new RAlmoxarifadoTipoItem;
        $this->obRAlmoxarifadoControleEstoque = new RAlmoxarifadoControleEstoque( $this );
        $this->obRAlmoxarifadoClassificacao = new RAlmoxarifadoCatalogoClassificacao();
        $this->obRUnidadeMedida = new RUnidadeMedida();
        $this->obRCadastroDinamico = new RCadastroDinamico();
        $this->obRCadastroDinamico->setPersistenteAtributos( new TAlmoxarifadoAtributoCatalogoItem );
        $this->obRCadastroDinamico->setCodCadastro ( 1 );
        $this->obRCadastroDinamico->obRModulo->setCodModulo   ( 29 );
        $this->boUnidadeNaoInformado = true;
        $this->boTipoNaoInformado = true;

    }

    /**
        * Execute um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
    {
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");
        $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();
        $boTransacao = "";
        $stFiltro = '';

        if ($this->getCodigo()) {
           $stFiltro .= ' AND aci.cod_item = '.$this->getCodigo();
        }

        if ($this->getCodigoAlmoxarifado()) {
           $stFiltro .= ' AND spfc.cod_almoxarifado = '.$this->getCodigoAlmoxarifado(). "\n";
        }

        if ($this->getDescricao()) {
           if( strpos($this->getDescricao(),'%')!==false )
              $stFiltro .= " AND lower(aci.descricao) ilike lower('".$this->getDescricao()."') ";
           else
              $stFiltro .= " AND lower(aci.descricao) = lower('".$this->getDescricao()."') ";
        }
        if ($this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo()) {
           $stFiltro .= ' AND aci.cod_catalogo = '.$this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo();
        }
        if ($this->obRAlmoxarifadoTipoItem->getCodigo()) {
            $stFiltro .= ' AND aci.cod_tipo = '.$this->obRAlmoxarifadoTipoItem->getCodigo();
        }
        if ($this->obRAlmoxarifadoClassificacao->getCodigo()) {
            $stFiltro .= ' AND aci.cod_classificacao = '.$this->obRAlmoxarifadoClassificacao->getCodigo();
        }
        if ($this->getAtivo()) {
            $stFiltro .= ' AND aci.ativo = true';
        }
        if ($this->obRAlmoxarifadoClassificacao->getEstrutural()) {
            //$stFiltro .= " AND acc.cod_estrutural like publico.fn_mascarareduzida('".$this->obRAlmoxarifadoClassificacao->getEstrutural()."')||'%' ";
            $stFiltro .= " AND acc.cod_estrutural like '".$this->obRAlmoxarifadoClassificacao->getEstrutural()."'||'%' ";
        }
        if ($this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->getAtributosDinamicos()) {
           $arAtributos = $this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->getAtributosDinamicos();
           for ($i = 0; $i < count($arAtributos); $i++) {
              $stFiltro .= ' and exists ( select 1 from ';
              $stFiltro .= " almoxarifado.atributo_catalogo_classificacao_item_valor aacciv where \n";
              $stFiltro .= " aacciv.cod_item = aci.cod_item AND                \n";
              $stFiltro .= " aacciv.cod_classificacao = aci.cod_classificacao AND \n";
              $stFiltro .= " aacciv.cod_catalogo = aci.cod_catalogo               \n";
              $stFiltro .= ' and aacciv.cod_modulo = '.$this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->obRModulo->getCodModulo();
              $stFiltro .= ' and aacciv.cod_cadastro = '.$this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->getCodCadastro();
              $stFiltro .= ' and aacciv.cod_atributo = '.$arAtributos[$i]->getCodAtributo();
              $stFiltro .= " and aacciv.valor = '". $arAtributos[$i]->getValor()."' )";
           }
        }

        if (!($this->boServico)) {
            $stFiltro.=" AND aci.cod_tipo <> 3 ";
        }
        if (!($this->boUnidadeNaoInformado)) {
            $stFiltro.=" AND aci.cod_unidade <> 0 ";
        }
        if (!($this->boTipoNaoInformado)) {
            $stFiltro.=" AND aci.cod_tipo <> 0 ";
        }

        if ($this->getVerificarMovimentacaoItem() == true) {
            $stFiltro.=" AND not exists ( select lancamento_material.cod_item
                                            from almoxarifado.lancamento_material
                                           where lancamento_material.cod_item = aci.cod_item)";
        }
        if ($this->boSomenteComMovimentacao) {
            $stFiltro.=" AND     exists ( select lancamento_material.cod_item
                                            from almoxarifado.lancamento_material
                                           where lancamento_material.cod_item = aci.cod_item)";
        }

        $stGrouBy = "Group By
                      ac.cod_catalogo,
                      ac.descricao,
                      acc.cod_estrutural,
                      aci.cod_item,
                      ati.cod_tipo,
                      ati.descricao,
                      aci.descricao,
                      aum.cod_unidade,
                      aum.cod_grandeza,
                      aum.nom_unidade \n";
        $stFiltro = $stFiltro;

        if ( $this->getVerificaSaldo() ) {
            $obErro = $obTAlmoxarifadoCatalogoItem->recuperaRelacionamentoComSaldo( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        } elseif ($this->getCodigoAlmoxarifado()) {
            $obErro = $obTAlmoxarifadoCatalogoItem->recuperaRelacionamentoAlmoxarifado( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            } else {
                $obErro = $obTAlmoxarifadoCatalogoItem->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        }

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
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");
        $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();
        $obTAlmoxarifadoCatalogoItem->setDado("cod_item", $this->inCodigo);
        if ($this->boVerificaSaldo) {
            $obErro = $obTAlmoxarifadoCatalogoItem->recuperaPorChaveComSaldo( $rsRecordSet, $boTransacao );
        } else {
            $obErro = $obTAlmoxarifadoCatalogoItem->recuperaPorChave( $rsRecordSet, $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $this->setDescricao($rsRecordSet->getCampo("descricao"));
            $this->setDescricaoResumida($rsRecordSet->getCampo("descricao_resumida"));
            $this->setAtivo($rsRecordSet->getCampo("ativo")=='t'?true:false);
            $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo( $rsRecordSet->getCampo('cod_catalogo'));
            $this->obRAlmoxarifadoClassificacao->setCodigo( $rsRecordSet->getCampo ( 'cod_classificacao' ));
            if ( !$obErro->ocorreu() ) {
                $this->obRAlmoxarifadoClassificacao->consultar();

                $this->obRAlmoxarifadoTipoItem->setCodigo( $rsRecordSet->getCampo('cod_tipo'));
                $this->obRAlmoxarifadoTipoItem->consultar();
                $obErro = $this->obRAlmoxarifadoControleEstoque->consultar();
                if ( !$obErro->ocorreu() ) {
                    $this->obRUnidadeMedida->setCodUnidade( $rsRecordSet->getCampo('cod_unidade' ));
                    $this->obRUnidadeMedida->obRGrandeza->setCodGrandeza ( $rsRecordSet->getCampo('cod_grandeza') );
                    $this->obRUnidadeMedida->consultar($rsUnidade);
                    $this->obRUnidadeMedida->setNome($rsUnidade->getCampo('nom_unidade'));
                }
            }
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
        $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;

        $obTAlmoxarifadoCatalogoItem->setDado( "descricao"            , $this->getDescricao() );
        $obTAlmoxarifadoCatalogoItem->setDado( "descricao_resumida"   , $this->getDescricaoResumida() );
        $obTAlmoxarifadoCatalogoItem->setDado( "cod_classificacao", $this->obRAlmoxarifadoClassificacao->getCodigo());
        $obTAlmoxarifadoCatalogoItem->recuperaDescricao($rsDescricaoIgual);

        $obErro = new Erro;

        if ($rsDescricaoIgual->getNumLinhas()>0) {

            $obErro->setDescricao('Já existe este item com esta classificação');
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

            if ( !$obErro->ocorreu() ) {
                if ($this->inCodigo == "" || $this->inCodigo == null) {
                    $obErro = $obTAlmoxarifadoCatalogoItem->proximoCod( $this->inCodigo, $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {

                    $obTAlmoxarifadoCatalogoItem->setDado( "cod_catalogo", $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo() );
                    $obTAlmoxarifadoCatalogoItem->setDado( "cod_item" , $this->getCodigo());
                    $obTAlmoxarifadoCatalogoItem->setDado( "cod_tipo" , $this->obRAlmoxarifadoTipoItem->getCodigo());
                    $obTAlmoxarifadoCatalogoItem->setDado( "cod_classificacao", $this->obRAlmoxarifadoClassificacao->getCodigo());
                    $obTAlmoxarifadoCatalogoItem->setDado( "cod_unidade", $this->obRUnidadeMedida->getCodUnidade());
                    $obTAlmoxarifadoCatalogoItem->setDado( "cod_grandeza", $this->obRUnidadeMedida->obRGrandeza->getCodGrandeza());
                    $obTAlmoxarifadoCatalogoItem->setDado( "descricao"            , $this->stDescricao );
                    $obTAlmoxarifadoCatalogoItem->setDado( "descricao_resumida"   , $this->stDescricaoResumida );

                    $obErro = $obTAlmoxarifadoCatalogoItem->inclusao( $boTransacao );

                    if (!$obErro->ocorreu()) {
                      $obErro =  $this->obRAlmoxarifadoControleEstoque->incluir( $boTransacao);
                    }

                    if (!$obErro->ocorreu()) {
                       $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_item" => $this->getCodigo() ) );
                                       $this->obRCadastroDinamico->setCodCadastro(2);
                       $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                    }
                }
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoCatalogoItem );
            }
        }

        return $obErro;

    }

    /**
        * Alterar Item
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function alterar($boTransacao = "")
    {
        $boFlagTransacao = false;
        $rsNiveis = new RecordSet();
        $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoCatalogoItem->setDado( "cod_catalogo"    , $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo() );
            $obTAlmoxarifadoCatalogoItem->setDado( "cod_item"        , $this->getCodigo()            );
            $obTAlmoxarifadoCatalogoItem->setDado( "cod_tipo"         , $this->obRAlmoxarifadoTipoItem->getCodigo());
            $obTAlmoxarifadoCatalogoItem->setDado( "cod_classificacao"  , $this->obRAlmoxarifadoClassificacao->getCodigo());
            $obTAlmoxarifadoCatalogoItem->setDado( "cod_unidade",  $this->obRUnidadeMedida->getCodUnidade());
            $obTAlmoxarifadoCatalogoItem->setDado( "cod_grandeza",  $this->obRUnidadeMedida->obRGrandeza->getCodGrandeza());
            $obTAlmoxarifadoCatalogoItem->setDado( "descricao" , $this->getDescricao() );
            $obTAlmoxarifadoCatalogoItem->setDado( "descricao_resumida" , $this->getDescricaoResumida() );
            $obTAlmoxarifadoCatalogoItem->setDado( "ativo" , $this->getAtivo() );

            if (!$obErro->ocorreu()) {
                $obErro = $obTAlmoxarifadoCatalogoItem->alteracao( $boTransacao );
            }

            if (!$obErro->ocorreu()) {
                $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque;
                $obTAlmoxarifadoControleEstoque->setDado("cod_item", $this->getCodigo());
                $obTAlmoxarifadoControleEstoque->recuperaPorChave( $rsControleEstoque, $boTransacao );

                if ($rsControleEstoque->getNumLinhas() > 0) {
                    $obErro = $this->obRAlmoxarifadoControleEstoque->alterar( $boTransacao );
                } else {
                    $obErro = $this->obRAlmoxarifadoControleEstoque->incluir( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() ) {
               if ( !$obErro->ocorreu() ) {
                  // salva os valores dos atributos dinâmicos
                  $arChaveAtributoItem =  array( "cod_item" => $this->getCodigo() , "cod_classificacao" => $this->obRAlmoxarifadoClassificacao->getCodigo(), "cod_catalogo" => $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo());
                  $this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoItem );
                  $obErro = $this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->alterarValores( $boTransacao );
              }
            }

            if (!$obErro->ocorreu()) {
               $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_item" => $this->getCodigo() ) );
               $this->obRCadastroDinamico->setCodCadastro(2);
               $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
            }

            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoCatalogoItem );
        }

        return $obErro;
    }

    /**
        * Exclui Catalogo
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function excluir($boTransacao = "")
    {
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItemMarca.class.php" );
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLocalizacaoFisicaItem.class.php" );
        $obTAlmoxarifadoCatalogoItemMarca     = new TAlmoxarifadoCatalogoItemMarca;
        $obTAlmoxarifadoLocalizacaoFisicaItem = new TAlmoxarifadoLocalizacaoFisicaItem;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();
        if ( !$obErro->ocorreu() ) {
            // exclui os valores dos atributos dinâmicos
            $arChaveAtributoItem =  array( "cod_item" => $this->getCodigo() , "cod_classificacao" => $this->obRAlmoxarifadoClassificacao->getCodigo(), "cod_catalogo" => $this->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo());
            $this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoItem );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRAlmoxarifadoClassificacao->obRCadastroDinamico->excluirValores( $boTransacao );
            $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_item" => $this->getCodigo() ) );
            $this->obRCadastroDinamico->setCodCadastro(2);
            $obErro = $this->obRCadastroDinamico->excluir( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoCatalogoItem->setDado( "cod_item", $this->getCodigo());
            $obErro = $this->obRAlmoxarifadoControleEstoque->excluir( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            //exclui o item das localizações
            $obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_item", $this->getCodigo() );
            $obErro = $obTAlmoxarifadoLocalizacaoFisicaItem->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            //excluir o item/marcas
            $obTAlmoxarifadoCatalogoItemMarca->setDado( "cod_item", $this->getCodigo() );
            $obErro = $obTAlmoxarifadoCatalogoItemMarca->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTAlmoxarifadoCatalogoItem->exclusao( $boTransacao );
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoCatalogo );

        return $obErro;
    }

}
