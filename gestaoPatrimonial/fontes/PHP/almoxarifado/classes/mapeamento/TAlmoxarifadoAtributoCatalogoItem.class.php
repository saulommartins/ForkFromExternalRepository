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
    * Classe de mapeamento da tabela ALMOXARIFADO.ATRIBUTO_CATALOGO_ITEM
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TAlmoxarifadoAtributoCatalogoItem.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

/*
$Log: TAlmoxarifadoAtributoCatalogoItem.class.php,v $
Revision 1.10  2007/07/20 19:47:26  hboaventura
Correção do programa

Revision 1.9  2007/07/17 14:50:28  hboaventura
Bug#9160#

Revision 1.8  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.ATRIBUTO_CATALOGO_ITEM
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoAtributoCatalogoItem extends PersistenteAtributos
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoAtributoCatalogoItem()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.atributo_catalogo_item');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_item,cod_atributo,cod_cadastro,cod_modulo');

    $this->AddCampo('cod_item','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('ativo','boolean',true,'',false,false);

}

function recuperaAtributoCatalogoItem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAtributoCatalogoItem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAtributoCatalogoItem()
{
    $stSql.= "
         SELECT atributo_valor_padrao.valor_padrao
              , atributo_dinamico.cod_modulo
              , atributo_dinamico.cod_cadastro
              , atributo_dinamico.cod_atributo
              , atributo_dinamico.cod_tipo
              , atributo_dinamico.nao_nulo
              , atributo_dinamico.nom_atributo
              , atributo_dinamico.mascara
              , atributo_dinamico.ajuda
              , atributo_dinamico.ativo
              , atributo_dinamico.interno
              , atributo_dinamico.indexavel
          FROM  almoxarifado.atributo_catalogo_item
    INNER JOIN  administracao.atributo_dinamico
            ON  atributo_dinamico.cod_atributo = atributo_catalogo_item.cod_atributo
           AND  atributo_dinamico.cod_cadastro = atributo_catalogo_item.cod_cadastro
           AND  atributo_dinamico.cod_modulo = atributo_catalogo_item.cod_modulo
     LEFT JOIN  administracao.atributo_valor_padrao
            ON  atributo_valor_padrao.cod_modulo = atributo_dinamico.cod_modulo
           and  atributo_valor_padrao.cod_cadastro = atributo_dinamico.cod_cadastro
           and  atributo_valor_padrao.cod_atributo = atributo_dinamico.cod_atributo
    INNER JOIN   almoxarifado.catalogo_item
        ON  catalogo_item.cod_item = atributo_catalogo_item.cod_item
         WHERE  atributo_catalogo_item.cod_modulo = 29
           AND  atributo_catalogo_item.cod_cadastro = 2
           AND  atributo_dinamico.ativo = true
           AND  atributo_catalogo_item.ativo = true
    ";

    if ( $this->getDado('cod_item') ) {
        $stFiltro = " AND atributo_catalogo_item.cod_item =".$this->getDado('cod_item');
    }

    return $stSql.$stFiltro;
}

function recuperaAtributoValoresCatalogoItem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAtributoValoresCatalogoItem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function recuperaAtributoDinamicoItem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAtributoDinamicoItem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAtributoValoresCatalogoItem()
{
    $stSql.= "SELECT atributo_estoque_material_valor.valor \n";
    $stSql.= " 	   , atributo_dinamico.cod_modulo \n";
    $stSql.= " 	   , atributo_dinamico.cod_cadastro \n";
    $stSql.= "	   , atributo_dinamico.cod_atributo \n";
    $stSql.= "	   , atributo_dinamico.cod_tipo \n";
    $stSql.= "	   , atributo_dinamico.nao_nulo \n";
    $stSql.= "	   , atributo_dinamico.nom_atributo \n";
    $stSql.= "	   , atributo_dinamico.mascara \n";
    $stSql.= "	   , atributo_dinamico.ajuda \n";
    $stSql.= "	   , atributo_dinamico.ativo \n";
    $stSql.= "	   , atributo_dinamico.interno \n";
    $stSql.= "	   , atributo_dinamico.indexavel \n";
    $stSql.= " FROM  almoxarifado.atributo_catalogo_item \n";
    $stSql.= " INNER JOIN administracao.atributo_dinamico \n";
    $stSql.= " 		   ON atributo_dinamico.cod_atributo = atributo_catalogo_item.cod_atributo \n";
    $stSql.= " 		  AND atributo_dinamico.cod_cadastro = atributo_catalogo_item.cod_cadastro \n";
    $stSql.= " 		  AND atributo_dinamico.cod_modulo = atributo_catalogo_item.cod_modulo \n";
    $stSql.= " left JOIN (SELECT *  \n";
    $stSql.= " 				FROM almoxarifado.atributo_estoque_material_valor \n";

    $stSql.= " 			   WHERE atributo_estoque_material_valor.cod_item = ".$this->getDado('cod_item')." \n";

    if ($this->getDado('cod_marca')) {
        $stSql.= " 				 AND atributo_estoque_material_valor.cod_marca = ".$this->getDado('cod_marca')." \n";
    }

    if ($this->getDado('cod_centro')) {
        $stSql.= "           	 AND atributo_estoque_material_valor.cod_centro = ".$this->getDado('cod_centro')." \n";
    }

    if ($this->getDado('cod_almoxarifado')) {
        $stSql.= " 				 AND atributo_estoque_material_valor.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." \n";
    }

    $stSql.= " 			 ) as atributo_estoque_material_valor \n";
    $stSql.= " 		  ON atributo_estoque_material_valor.cod_atributo = atributo_catalogo_item.cod_atributo \n";
    $stSql.= " 		 AND atributo_estoque_material_valor.cod_cadastro = atributo_catalogo_item.cod_cadastro \n";
    $stSql.= " 		 AND atributo_estoque_material_valor.cod_modulo = atributo_catalogo_item.cod_modulo \n";
    $stSql.= " 		 AND atributo_estoque_material_valor.cod_item  = atributo_catalogo_item.cod_item \n";
    $stSql.= " WHERE atributo_catalogo_item.cod_modulo = 29 \n";
    $stSql.= "   AND atributo_catalogo_item.cod_cadastro = 2 \n";
    $stSql.= " 	 AND atributo_dinamico.ativo = true \n";
    $stSql.= " 	 AND atributo_catalogo_item.ativo = true \n";
    $stSql.= " 	 AND atributo_catalogo_item.cod_item = ".$this->getDado('cod_item')."\n";
    if ($this->getDado('cod_marca')) {
        $stSql.= "AND atributo_estoque_material_valor.cod_marca = ".$this->getDado('cod_marca')." \n";
    }

    if ($this->getDado('cod_centro')) {
        $stSql.= "AND atributo_estoque_material_valor.cod_centro = ".$this->getDado('cod_centro')." \n";
    }

    if ($this->getDado('cod_almoxarifado')) {
        $stSql.= "AND atributo_estoque_material_valor.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." \n";
    }

    return $stSql;
}

function montaRecuperaAtributoDinamicoItem()
{

    $stSql = "SELECT AD.nao_nulo
           , AD.nom_atributo
           , AD.cod_atributo
           , AD.cod_cadastro
           , AD.cod_modulo
           , administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'') as valor_padrao
           , CASE TA.cod_tipo
                WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))
                WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,''))
                ELSE null
             END AS valor_padrao_desc
           , CASE TA.cod_tipo
                WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo, AD.cod_cadastro,'')
                ELSE  null
              END AS valor_desc
           , AD.ajuda
           , AD.mascara
           , TA.nom_tipo
           , TA.cod_tipo
        FROM administracao.atributo_dinamico AS AD
           , administracao.tipo_atributo AS TA
           , almoxarifado.atributo_catalogo_item AS AC
           WHERE AD.cod_tipo = TA.cod_tipo
         AND AD.cod_modulo   = 29
         AND AD.cod_cadastro = 2
         AND AD.ativo = 't'
         AND AC.ativo = 't'
         AND AD.cod_atributo = ac.cod_atributo
         AND AD.cod_cadastro = ac.cod_cadastro
         AND AD.cod_modulo = ac.cod_modulo";

    if ( $this->getDado('cod_item') ) {
        $stFiltro = " AND AC.cod_item =".$this->getDado('cod_item');
    }

    return $stSql.$stFiltro;
}

function recuperaAtributoItemCatalogoItemSimples(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAtributoItemCatalogoItemSimples",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAtributoItemCatalogoItemSimples()
{
    $stSql.= "SELECT * \n";
    $stSql.= " FROM  almoxarifado.atributo_catalogo_item \n";
    $stSql.= " INNER JOIN administracao.atributo_dinamico \n";
    $stSql.= " 		   ON atributo_dinamico.cod_atributo = atributo_catalogo_item.cod_atributo \n";
    $stSql.= " 		  AND atributo_dinamico.cod_cadastro = atributo_catalogo_item.cod_cadastro \n";
    $stSql.= " 		  AND atributo_dinamico.cod_modulo = atributo_catalogo_item.cod_modulo \n";
    $stSql.= " WHERE atributo_catalogo_item.cod_modulo = 29 \n";
    $stSql.= "   AND atributo_catalogo_item.cod_cadastro = 2 \n";
    $stSql.= " 	 AND atributo_dinamico.ativo = true \n";
    $stSql.= " 	 AND atributo_catalogo_item.ativo = true \n";
    $stSql.= " 	 AND atributo_catalogo_item.cod_item = ".$this->getDado('cod_item')."";
    $stSql.= " ORDER BY atributo_catalogo_item.cod_atributo";

    return $stSql;
}

function recuperaTipoAtributo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaTipoAtributo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaTipoAtributo()
{
    $stSql = "SELECT cod_tipo
                FROM administracao.atributo_dinamico
               WHERE cod_atributo = ".$this->getDado('cod_atributo')."  \n";

    return $stSql;
}

}
