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
    * Classe de mapeamento da tabela ARRECADACAO.GRUPO_CREDITO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRGrupoCredito.class.php 62125 2015-03-30 17:28:42Z jean $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.10  2007/03/07 20:58:33  cassiano
Bug #8441#

Revision 1.9  2006/10/19 18:45:09  cercato
setando ano exercicio como complemento.

Revision 1.8  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.7  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.GRUPO_CREDITO
  * Data de Criação: 12/05/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Lucas Teixeira Stephanou

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRGrupoCredito extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRGrupoCredito()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.grupo_credito');

    $this->setCampoCod('cod_grupo');
    $this->setComplementoChave('cod_grupo,ano_exercicio');
                  //nome,           tipo         requerido   Tamanho   PK     FK    Conteudo = ''
    $this->AddCampo('cod_grupo'     ,'integer'  ,true       ,''     ,true   ,false );
    $this->AddCampo('cod_modulo'    ,'integer'  ,true       ,''     ,false  ,true  );
    $this->AddCampo('ano_exercicio','char'  ,true       ,'4'    ,true  ,false );
    $this->AddCampo('descricao'     ,'varchar'  ,true       ,'80'   ,false  ,false );

}

public function recuperaListaExercicio(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaExercicio();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaExercicio()
    {
        $stSql = "SELECT distinct ano_exercicio
                    FROM arrecadacao.grupo_credito
                  ORDER BY ano_exercicio DESC
                ";

        return $stSql;
    }

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_grupo ";
    $stSql  = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
$stSql  = " SELECT                                          \r\n";
$stSql .= "     acg.*,                                      \r\n";
$stSql .= "     CASE                                        \r\n";
$stSql .= "        WHEN                                     \r\n";
$stSql .= "            acf.cod_grupo IS NULL                \r\n";
$stSql .= "        THEN                                     \r\n";
$stSql .= "            ''                                   \r\n";
$stSql .= "        ELSE                                     \r\n";
$stSql .= "            '!! ATENÇÃO !! - Há Um ou Mais Calendários Fiscal vinculados a este grupo' \r\n";
$stSql .= "    END AS calendario                            \r\n";
$stSql .= "    , regra_desoneracao_grupo.cod_funcao AS cod_des
               , funcao.nom_funcao AS func_des            ";

$stSql .= " FROM                                            \r\n";
$stSql .= "     arrecadacao.grupo_credito as acg            \r\n";

$stSql .= " LEFT JOIN
                arrecadacao.regra_desoneracao_grupo
            ON
                regra_desoneracao_grupo.cod_grupo = acg.cod_grupo
                AND regra_desoneracao_grupo.ano_exercicio = acg.ano_exercicio

            LEFT JOIN
                administracao.funcao
            ON
                funcao.cod_modulo = 25
                AND funcao.cod_biblioteca = 2
                AND funcao.cod_funcao = regra_desoneracao_grupo.cod_funcao ";

$stSql .= "     LEFT JOIN                                   \r\n";
$stSql .= "         arrecadacao.calendario_fiscal as acf    \r\n";
$stSql .= "     ON                                          \r\n";
$stSql .= "         acf.cod_grupo = acg.cod_grupo           \r\n";
$stSql .= "     and acf.ano_exercicio = acg.ano_exercicio   \r\n";

    return $stSql;
}

function recuperaMaxCodGrupo(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaMaxCodGrupo();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxCodGrupo()
{
    $stSql  = " SELECT                                          \r\n";
    $stSql .= "     MAX(acg.cod_grupo) AS max_cod_grupo         \r\n";
    $stSql .= " FROM                                            \r\n";
    $stSql .= "     arrecadacao.grupo_credito as acg            \r\n";

    return $stSql;
}

function recuperaParametroCalculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaParametroCalculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaParametroCalculo()
{
    $stSQL .= " SELECT                                                              \n";
    $stSQL .= "     grupo_credito.cod_grupo                                         \n";
    $stSQL .= "     ,grupo_credito.ano_exercicio                                    \n";
    $stSQL .= "     ,grupo_credito.cod_modulo                                       \n";
    $stSQL .= "     ,grupo_credito.descricao                                        \n";
    $stSQL .= "     ,credito_grupo.*                                                \n";
    $stSQL .= "     ,CASE WHEN parametro_calculo.ocorrencia_credito IS NULL THEN    \n";
    $stSQL .= "          false                                                      \n";
    $stSQL .= "      ELSE true END AS calculo                                       \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "     arrecadacao.grupo_credito                                       \n";
    $stSQL .= " INNER JOIN                                                          \n";
    $stSQL .= "     arrecadacao.credito_grupo                                       \n";
    $stSQL .= " ON                                                                  \n";
    $stSQL .= "     grupo_credito.cod_grupo         = credito_grupo.cod_grupo       \n";
    $stSQL .= "     AND grupo_credito.ano_exercicio = credito_grupo.ano_exercicio   \n";
    $stSQL .= " LEFT JOIN                                                           \n";
    $stSQL .= "           arrecadacao.parametro_calculo                             \n";
    $stSQL .= " ON                                                                  \n";
    $stSQL .= "     credito_grupo.cod_credito      = parametro_calculo.cod_credito  \n";
    $stSQL .= "     AND credito_grupo.cod_especie  = parametro_calculo.cod_especie  \n";
    $stSQL .= "     AND credito_grupo.cod_genero   = parametro_calculo.cod_genero   \n";
    $stSQL .= "     AND credito_grupo.cod_natureza = parametro_calculo.cod_natureza \n";

    return $stSQL;
}

}
?>
