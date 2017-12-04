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
* Classe de Mapeamento para tabela atributo_norma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  SW_ATRIBUTO_TIPO_NORMA
  * Data de Criação: 26/05/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TAtributoNorma extends PersistenteAtributos
{
/**
    * Método Construtor
    * @access Private
*/
function TAtributoNorma()
{
    parent::Persistente();
    $this->setTabela('normas.atributo_norma');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atributo,cod_cadastro');

    $this->AddCampo('cod_atributo'   ,'integer',true,'',true,true);
    $this->AddCampo('cod_cadastro'   ,'integer',true,'',true,true);
    $this->AddCampo('ativo'          ,'boolean',true,'',false,false);

}

/**
    * Método que sobreposto para recuperar atributos
    * @access Private
    * @return String  Comando SQL
*
function montaRecuperaAtributos()
{
    $stSql  = "  SELECT                                                      \n";
    $stSql .= "     AD.nao_nulo,                                             \n";
    $stSql .= "     AD.nom_atributo,                                         \n";
    $stSql .= "     AD.cod_atributo,                                         \n";
    $stSql .= "     AD.valor_padrao,                                         \n";
    $stSql .= "     AD.ajuda,                                                \n";
    $stSql .= "     AD.mascara,                                              \n";
    $stSql .= "     TA.nom_tipo                                              \n";
    $stSql .= "  FROM                                                        \n";
    $stSql .= "     sw_atributo_dinamico             AS AD,                 \n";
    $stSql .= "     sw_tipo_atributo                 AS TA                  \n";
    $stSql .= "  WHERE   AD.cod_tipo = TA.cod_tipo                           \n";
    $stSql .= "  AND     AD.cod_modulo    = ".$this->getDado('cod_modulo')." \n";

    return $stSql;
}
/**
    * Método que sobreposto para recuperar atributos disponiveis
    * @access Private
    * @return String  Comando SQL
*
function montaRecuperaAtributosDisponiveis()
{
    $stSql  = "  SELECT                                                      \n";
    $stSql .= "     AD.nao_nulo,                                             \n";
    $stSql .= "     AD.nom_atributo,                                         \n";
    $stSql .= "     AD.cod_atributo,                                         \n";
    $stSql .= "     AD.valor_padrao,                                         \n";
    $stSql .= "     AD.ajuda,                                                \n";
    $stSql .= "     AD.mascara,                                              \n";
    $stSql .= "     TA.nom_tipo                                              \n";
    $stSql .= "  FROM                                                        \n";
    $stSql .= "     sw_atributo_dinamico             AS AD,                 \n";
    $stSql .= "     sw_tipo_atributo                 AS TA                  \n";
    $stSql .= "  WHERE   AD.cod_tipo = TA.cod_tipo                           \n";
    $stSql .= "  AND     AD.cod_atributo NOT IN (                            \n";
    $stSql .= "     SELECT cod_atributo FROM                                 \n";
    $stSql .= "     ".$this->getTabela()."                                   \n";
    $stSql .= "     WHERE cod_cadastro  =".$this->getDado('cod_cadastro')."  \n";
    $stSql .= "     AND   cod_norma=".$this->getDado('cod_norma')."\n";
    $stSql .= "     AND   ativo = 't')                                       \n";
    $stSql .= "  AND    AD.cod_modulo = ".$this->getDado('cod_modulo')."     \n";

    return $stSql;
}
/**
    * Método que sobreposto para recuperar atributos selecionados
    * @access Private
    * @return String  Comando SQL
*
function montaRecuperaAtributosSelecionados()
{
    $stSql  = "  SELECT                                                  \n";
    $stSql .= "     ACA.cod_cadastro,                                    \n";
    $stSql .= "     ACA.cod_atributo,                                    \n";
    $stSql .= "     ACA.ativo,                                           \n";
    $stSql .= "     AD.nao_nulo,                                         \n";
    $stSql .= "     AD.nom_atributo,                                     \n";
    $stSql .= "     AD.valor_padrao,                                     \n";
    $stSql .= "     AD.ajuda,                                            \n";
    $stSql .= "     AD.mascara,                                          \n";
    $stSql .= "     TA.nom_tipo                                          \n";
    $stSql .= "  FROM                                                    \n";
    $stSql .= "     ".$this->getTabela()." AS ACA,                       \n";
    $stSql .= "     sw_atributo_dinamico             AS AD,             \n";
    $stSql .= "     sw_tipo_atributo                 AS TA              \n";
    $stSql .= "  WHERE                                                   \n";
    $stSql .= "     ACA.cod_atributo = AD.cod_atributo                   \n";
    $stSql .= "  AND TA.cod_tipo = AD.cod_tipo                           \n";
    $stSql .= "  AND ACA.ativo = true                                    \n";
    $stSql .= "  AND AD.cod_modulo   =".$this->getDado('cod_modulo')."   \n";
    $stSql .= "  AND ACA.cod_cadastro=".$this->getDado('cod_cadastro')." \n";
    $stSql .= "  AND ACA.cod_norma=".$this->getDado('cod_norma')."\n";

    return $stSql;
}
*/
}
