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
    * Classe de mapeamento da tabela ALMOXARIFADO.CATALOGO_CLASSIFICACAO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 24716 $
    $Name$
    $Author: bruce $
    $Date: 2007-08-13 17:38:53 -0300 (Seg, 13 Ago 2007) $

    * Casos de uso: uc-03.03.05
*/

/*
$Log$
Revision 1.22  2007/08/13 20:37:25  bruce
Bug#9813#

Revision 1.21  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.20  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.CATALOGO_CLASSIFICACAO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoCatalogoClassificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoCatalogoClassificacao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.catalogo_classificacao');

    $this->setCampoCod('cod_classificacao');
    $this->setComplementoChave('cod_catalogo');

    $this->AddCampo('cod_classificacao','integer',true,'',true,false);
    $this->AddCampo('cod_catalogo','integer',true,'',true,false);
    $this->AddCampo('cod_estrutural','varchar',true,'160',false,false);
    $this->AddCampo('descricao','varchar',true,'160',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                         \n";
    $stSql .= "  cln.cod_classificacao                                        \n";
    $stSql .= ", cln.cod_nivel  as cod_nivel                                  \n";
    $stSql .= ", cal.descricao  as descricao                                  \n";
    $stSql .= ", lpad(cal.cod_estrutural, char_length(cn.mascara), '0') as mascara \n";
    $stSql .= "FROM                                                           \n";
    $stSql .= "almoxarifado.catalogo_classificacao cal,                       \n";
    $stSql .= "almoxarifado.classificacao_nivel cln,                          \n";
    $stSql .= "almoxarifado.catalogo_niveis cn                                \n";
    $stSql .= "WHERE cal.cod_classificacao = cln.cod_classificacao            \n";
    $stSql .= "  AND cal.cod_catalogo = cln.cod_catalogo                      \n";
    $stSql .= "  AND cn.cod_catalogo  = cln.cod_catalogo                      \n";
    $stSql .= "  AND cn.nivel = cln.nivel                                     \n";

    return $stSql;
}

function recuperaProximoEstrutural(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaProximoEstrutural().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProximoEstrutural()
{
    $stSql =  " select almoxarifado.fn_retorna_proximo_estrutural_livre(".$this->getDado("cod_catalogo").",".$this->getDado("nivel").",'".$this->getDado("cod_estrutural_mae")."') as livre ";
    //$stSql = "select  as livre";
/*
    $stSql .= "select                                                                                                                          \n";
    $stSql .= "to_number(                                                                                                                      \n";
    $stSql .= "max(                                                                                                                            \n";
    $stSql .= "case when cod_estrutural is null  then '0'                                                                                           \n";
    $stSql .= "else substr( cod_estrutural, 0, ( case when strpos(cod_estrutural,'.') = 0 then 99999 else strpos(cod_estrutural,'.') end)) end \n";
    $stSql .= ") , '99999999' )+1 as prox_cod_estrutural                                                                                       \n";
    $stSql .= "from (                                                                                                                          \n";
    $stSql .= "select                                                                                                                          \n";
    $stSql .= "substr( cod_estrutural, length(publico.substring_estrutural(cod_estrutural,'.', " . $this->getDado("nivel") . ")) ) as cod_estrutural   \n";
    $stSql .= "from                                                                                                                            \n";
    $stSql .= "almoxarifado.catalogo_classificacao                                                                                             \n";

    if ($this->getDado("codEstruturalMae")) {
        $stSql .= "where                                                                                                    \n";
        $stSql .= "cod_estrutural like publico.fn_mascara_dinamica('" . $this->getDado("mascaraCompleta") . "','" . $this->getDado("codEstruturalMae") . "')||'%'";
    }

    $stSql .= ") as tabela                                                                                                  \n";
*/

    return $stSql;
}

function recuperaDetalhesClassificacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDetalhesClassificacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDetalhesClassificacao()
{
    $stSql  = "SELECT                                                                      \n";
    $stSql .= "            cc.cod_catalogo as cod_catalogo,                                \n";
    $stSql .= "            cn.nivel as nivel,                                              \n";
    $stSql .= "            cn.cod_nivel as cod_nivel,                                      \n";
    $stSql .= "            cc.cod_estrutural as cod_estrutural,                            \n";
    $stSql .= "            cc.descricao as descricao,                                       \n";
    $stSql .= "            cc.cod_classificacao as cod_classificacao                       \n";
    $stSql .= "from                                                                        \n";
    $stSql .= "            almoxarifado.catalogo_classificacao cc,                         \n";
    $stSql .= "            almoxarifado.classificacao_nivel cn                             \n";

    return $stSql;
}

function recuperaDadosNivel(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosNivel().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosNivel()
{
    $stSql .= "select                                                     \n";
    $stSql .= "            cod_nivel                                      \n";
    $stSql .= "from                                                       \n";
    $stSql .= "            almoxarifado.classificacao_nivel               \n";

    return $stSql;
}

function recuperaMascara(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMascara().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMascara()
{
    $stSql  = "select                                                                      \n";
    $stSql .= "            mascara                                                         \n";
    $stSql .= "from                                                                        \n";
    $stSql .= "            almoxarifado.catalogo_niveis                                    \n";

    return $stSql;
}

function recuperaMae(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMae().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMae()
{
    $stSql  = "SELECT                                           \n";
    $stSql .= "        ca.descricao as descricao_nivel,         \n";
    $stSql .= "        cc.cod_estrutural as cod_estrutural,     \n";
    $stSql .= "        cc.descricao as descricao_classificacao, \n";
    $stSql .= "        cn.cod_nivel as cod_nivel                \n";
    $stSql .= "FROM                                             \n";
    $stSql .= "        almoxarifado.catalogo_niveis ca,         \n";
    $stSql .= "        almoxarifado.catalogo_classificacao cc,  \n";
    $stSql .= "        almoxarifado.classificacao_nivel cn      \n";

    return $stSql;
}

function recuperaCodigoClassificacaoImportacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaCodigoClassificacaoImportacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCodigoClassificacaoImportacao()
{
    $stSql  = "select                                                                      \n";
    $stSql .= "            cod_classificacao                                               \n";
    $stSql .= "from                                                                        \n";
    $stSql .= "            almoxarifado.catalogo_classificacao                             \n";

    return $stSql;
}

function recuperaClassificacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaClassificacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaClassificacao()
{
    $stSql  = " SELECT  *                                                                   \n";
    $stSql .= " FROM    almoxarifado.fn_lista_classificacoes_mae(".$this->getDado("cod_catalogo").",'".$this->getDado("cod_estrutural")."') \n";
    $stSql .= " ORDER BY cod_estrutural                                                     \n";

    return $stSql;
}

function montaRecuperaTodos()
{
    $stSql = " SELECT cod_classificacao ,
                      cod_catalogo ,
                      cod_estrutural ,
                      descricao,
                      publico.fn_nivel(cod_estrutural) as nivel
                    FROM
                        almoxarifado.catalogo_classificacao
             ";

    return $stSql;
}

}
