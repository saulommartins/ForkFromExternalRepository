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
* Classe de mapeamento para administracao.cacastro
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.CADASTRO
  * Data de Criação: 09/08/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoCadastro extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoCadastro()
{
    parent::Persistente();
    $this->setTabela('administracao.cadastro');

    $this->setCampoCod('cod_cadastro');
    $this->setComplementoChave('cod_modulo');

    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,false);
    $this->AddCampo('nom_cadastro','varchar',true,'80',false,false);
    $this->AddCampo('mapeamento','varchar',true,'80',false,false);

}

function recuperaAtributosSemFuncao(&$rsRecordSet,$stFiltro = "", $stOrdem = "", $obTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtributosSemFuncao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaAtributosSemFuncao()
{
    $stSql  = " SELECT                                       \n";
    $stSql .= "    ad.cod_modulo,                            \n";
    $stSql .= "    ad.cod_atributo,                          \n";
    $stSql .= "    ad.cod_tipo,                              \n";
    $stSql .= "    ad.nao_nulo,                              \n";
    $stSql .= "    ad.nom_atributo,                          \n";
    $stSql .= "    ad.valor_padrao,                          \n";
    $stSql .= "    ad.ajuda,                                 \n";
    $stSql .= "    ad.mascara,                               \n";
    $stSql .= "    ad.ativo,                                 \n";
    $stSql .= "    ad.interno,                               \n";
    $stSql .= "    ad.indexavel,                             \n";
    $stSql .= "    c.nom_cadastro                            \n";
    $stSql .= " FROM                                         \n";
    $stSql .= "     administracao.cadastro as c,             \n";
    $stSql .= "     administracao.atributo_dinamico as ad    \n";
    $stSql .= " LEFT JOIN                                    \n";
    $stSql .= "     administracao.atributo_funcao as af       \n";
    $stSql .= " ON                                           \n";
    $stSql .= "     af.cod_modulo    = ad.cod_modulo     AND \n";
    $stSql .= "     af.cod_cadastro  = ad.cod_cadastro   AND \n";
    $stSql .= "     af.cod_atributo  = ad.cod_atributo       \n";
    $stSql .= " WHERE                                        \n";
    $stSql .= "     ad.cod_modulo     = c.cod_modulo     AND \n";
    $stSql .= "     ad.cod_cadastro   = c.cod_cadastro   AND \n";
    $stSql .= "     af.cod_modulo     IS NULL AND            \n";
    $stSql .= "     af.cod_biblioteca IS NULL AND            \n";
    $stSql .= "     af.cod_cadastro   IS NULL AND            \n";
    $stSql .= "     af.cod_atributo   IS NULL                \n";

    return $stSql;
}

function recuperaAtributosComFuncao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $obTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtributosComFuncao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaAtributosComFuncao()
{
    $stSql  = " SELECT                                       \n";
    $stSql .= "    b.cod_modulo,                             \n";
    $stSql .= "    b.cod_biblioteca,                         \n";
    $stSql .= "    b.nom_biblioteca,                         \n";
    $stSql .= "    f.cod_tipo_retorno,                       \n";
    $stSql .= "    f.cod_funcao,                             \n";
    $stSql .= "    f.nom_funcao,                             \n";
    $stSql .= "    ad.cod_atributo,                          \n";
    $stSql .= "    ad.cod_tipo,                              \n";
    $stSql .= "    ad.nao_nulo,                              \n";
    $stSql .= "    ad.nom_atributo,                          \n";
    $stSql .= "    ad.valor_padrao,                          \n";
    $stSql .= "    ad.ajuda,                                 \n";
    $stSql .= "    ad.mascara,                               \n";
    $stSql .= "    ad.ativo,                                 \n";
    $stSql .= "    ad.interno,                               \n";
    $stSql .= "    ad.indexavel,                             \n";
    $stSql .= "    c.nom_cadastro                            \n";
    $stSql .= " FROM                                         \n";
    $stSql .= "     administracao.biblioteca as b,           \n";
    $stSql .= "     administracao.funcao as f,               \n";
    $stSql .= "     administracao.atributo_funcao as af,     \n";
    $stSql .= "     administracao.atributo_dinamico as ad,   \n";
    $stSql .= "     administracao.cadastro as c              \n";
    $stSql .= " WHERE                                        \n";
    $stSql .= "     b.cod_modulo     = f.cod_modulo      AND \n";
    $stSql .= "     b.cod_biblioteca = f.cod_biblioteca  AND \n";
    $stSql .= "     f.cod_modulo     = af.cod_modulo     AND \n";
    $stSql .= "     f.cod_biblioteca = af.cod_biblioteca AND \n";
    $stSql .= "     f.cod_funcao     = af.cod_funcao     AND \n";
    $stSql .= "     af.cod_modulo    = ad.cod_modulo     AND \n";
    $stSql .= "     af.cod_cadastro  = ad.cod_cadastro   AND \n";
    $stSql .= "     af.cod_atributo  = ad.cod_atributo   AND \n";
    $stSql .= "     ad.cod_modulo    = c.cod_modulo      AND \n";
    $stSql .= "     ad.cod_cadastro  = c.cod_cadastro        \n";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                       \n";
    $stSql .= "     g.cod_gestao,                            \n";
    $stSql .= "     m.cod_modulo,                            \n";
    $stSql .= "     c.cod_cadastro,                          \n";
    $stSql .= "     g.nom_gestao,                            \n";
    $stSql .= "     m.nom_modulo,                            \n";
    $stSql .= "     c.nom_cadastro,                          \n";
    $stSql .= "     g.nom_diretorio as nom_diretorio_gestao, \n";
    $stSql .= "     m.nom_diretorio as nom_diretorio_modulo, \n";
    $stSql .= "     c.mapeamento                             \n";
    $stSql .= " FROM                                         \n";
    $stSql .= "     ADMINISTRACAO.CADASTRO AS c,             \n";
    $stSql .= "     ADMINISTRACAO.MODULO   AS m,             \n";
    $stSql .= "     ADMINISTRACAO.GESTAO   AS g              \n";
    $stSql .= " WHERE                                        \n";
    $stSql .= "     c.cod_modulo = m.cod_modulo AND          \n";
    $stSql .= "     m.cod_gestao = g.cod_gestao              \n";

    return $stSql;
}

}
