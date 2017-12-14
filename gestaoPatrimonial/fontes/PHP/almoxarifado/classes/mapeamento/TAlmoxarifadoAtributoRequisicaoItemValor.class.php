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
    * Classe de mapeamento da tabela almoxarifado.atributo_requisicao_item_valor
    * Data de Criação: 27/02/2008

    * @author Andre Almeida

    * Casos de uso: uc-03.03.10

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAlmoxarifadoAtributoRequisicaoItemValor extends Persistente
{
    public function TAlmoxarifadoAtributoRequisicaoItemValor()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.atributo_requisicao_item_valor');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro, cod_sequencial, cod_modulo, cod_cadastro, cod_atributo');

        $this->AddCampo( 'exercicio'       , 'char'   , true, '4', true , "TAlmoxarifadoAtributoRequisicaoItem" );
        $this->AddCampo( 'cod_almoxarifado', 'integer', true, '' , true , "TAlmoxarifadoAtributoRequisicaoItem" );
        $this->AddCampo( 'cod_requisicao'  , 'integer', true, '' , true , "TAlmoxarifadoAtributoRequisicaoItem" );
        $this->AddCampo( 'cod_item'        , 'integer', true, '' , true , "TAlmoxarifadoAtributoRequisicaoItem" );
        $this->AddCampo( 'cod_marca'       , 'integer', true, '' , true , "TAlmoxarifadoAtributoRequisicaoItem" );
        $this->AddCampo( 'cod_centro'      , 'integer', true, '' , true , "TAlmoxarifadoAtributoRequisicaoItem" );
        $this->AddCampo( 'cod_sequencial'  , 'integer', true, '' , true , "TAlmoxarifadoAtributoRequisicaoItem" );
        $this->AddCampo( 'cod_modulo'      , 'integer', true, '' , true , true );
        $this->AddCampo( 'cod_cadastro'    , 'integer', true, '' , true , true );
        $this->AddCampo( 'cod_atributo'    , 'integer', true, '' , true , true );
        $this->AddCampo( 'valor'           , 'text'   , true, '' , false, false );
     }

    public function recuperaValoresAtributoRequisicao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaValoresAtributoRequisicao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaValoresAtributoRequisicao()
    {
        $stSql = "
            select atributo_requisicao_item_valor.exercicio
                 , atributo_requisicao_item_valor.cod_almoxarifado
                 , atributo_requisicao_item_valor.cod_requisicao
                 , atributo_requisicao_item_valor.cod_item
                 , atributo_requisicao_item_valor.cod_marca
                 , atributo_requisicao_item_valor.cod_centro
                 , atributo_requisicao_item_valor.cod_sequencial
                 , atributo_requisicao_item_valor.cod_atributo
                 , atributo_requisicao_item_valor.valor
                 , atributo_requisicao_item.quantidade
                 , atributo_dinamico.nom_atributo
            from almoxarifado.atributo_requisicao_item
            join almoxarifado.atributo_requisicao_item_valor
            using (exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro, cod_sequencial)
            join administracao.atributo_dinamico
            using (cod_modulo, cod_cadastro, cod_atributo)
        ";

        if ( $this->getDado('exercicio') ) {
            $stFiltro  = " and atributo_requisicao_item_valor.exercicio = ".$this->getDado('exercicio');
        }
        if ( $this->getDado('cod_almoxarifado') ) {
            $stFiltro .= " and atributo_requisicao_item_valor.cod_almoxarifado = ".$this->getDado('cod_almoxarifado');
        }
        if ( $this->getDado('cod_requisicao') ) {
            $stFiltro .= " and atributo_requisicao_item_valor.cod_requisicao = ".$this->getDado('cod_requisicao');
        }
        if ( $this->getDado('cod_item') ) {
            $stFiltro .= " and atributo_requisicao_item_valor.cod_item = ".$this->getDado('cod_item');
        }
        if ( $this->getDado('cod_marca') ) {
            $stFiltro .= " and atributo_requisicao_item_valor.cod_marca = ".$this->getDado('cod_marca');
        }
        if ( $this->getDado('cod_centro') ) {
            $stFiltro .= " and atributo_requisicao_item_valor.cod_centro = ".$this->getDado('cod_centro');
        }

        $stFiltro = " where ".substr( $stFiltro, 4 );

        return $stSql.$stFiltro;
    }

    public function recuperaAtributosItemRequisicao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaAtributosItemRequisicao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaAtributosItemRequisicao()
    {
        $stSql  ="SELECT atributo_requisicao_item_valor.*                                                                   \n";
        $stSql .="     , atributo_dinamico.nom_atributo                                                                     \n";
        $stSql .="     , atributo_requisicao_item.*                                                                         \n";
        $stSql .="  FROM almoxarifado.atributo_requisicao_item_valor                                                        \n";
        $stSql .=" INNER JOIN administracao.atributo_dinamico                                                               \n";
        $stSql .="    ON (atributo_requisicao_item_valor.cod_atributo = atributo_dinamico.cod_atributo                      \n";
        $stSql .="   AND atributo_requisicao_item_valor.cod_cadastro = atributo_dinamico.cod_cadastro                       \n";
        $stSql .="   AND atributo_requisicao_item_valor.cod_modulo = atributo_dinamico.cod_modulo)                          \n";

        $stSql .=" INNER JOIN almoxarifado.atributo_requisicao_item                                                         \n";
        $stSql .="    ON (atributo_requisicao_item_valor.cod_sequencial = atributo_requisicao_item.cod_sequencial           \n";
        $stSql .="   AND atributo_requisicao_item_valor.exercicio = atributo_requisicao_item.exercicio                      \n";
        $stSql .="   AND atributo_requisicao_item_valor.cod_requisicao = atributo_requisicao_item.cod_requisicao            \n";
        $stSql .="   AND atributo_requisicao_item_valor.cod_item = atributo_requisicao_item.cod_item                        \n";
        $stSql .="   AND atributo_requisicao_item_valor.cod_marca = atributo_requisicao_item.cod_marca                      \n";
        $stSql .="   AND atributo_requisicao_item_valor.cod_centro = atributo_requisicao_item.cod_centro                    \n";
        $stSql .="   AND atributo_requisicao_item_valor.cod_almoxarifado = atributo_requisicao_item.cod_almoxarifado)       \n";

        $stSql .=" WHERE atributo_requisicao_item_valor.cod_requisicao =".$this->getDado('cod_requisicao')."                \n";
        if ($this->getDado('cod_item')) {
            $stSql .="   AND atributo_requisicao_item_valor.cod_item =".$this->getDado('cod_item')."                            \n";
        }
        if ($this->getDado('cod_almoxarifado')) {
            $stSql .="   AND atributo_requisicao_item_valor.cod_almoxarifado =".$this->getDado('cod_almoxarifado')."            \n";
        }
        if ($this->getDado('cod_centro')) {
            $stSql .="   AND atributo_requisicao_item_valor.cod_centro   =".$this->getDado('cod_centro')."                      \n";
        }
        if ($this->getDado('cod_marca')) {
            $stSql .="   AND atributo_requisicao_item_valor.cod_marca    =".$this->getDado('cod_marca')."                       \n";
        }
        if ($this->getDado('cod_atributo')) {
            $stSql .="   AND atributo_requisicao_item_valor.cod_atributo =".$this->getDado('cod_atributo')."                    \n";
        }

        return $stSql;
    }

}
