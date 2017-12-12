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
    * Classe de mapeamento da tabela ARRECADACAO.CREDITO_GRUPO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCreditoGrupo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.25  2007/01/31 17:43:19  cercato
correcao do bug (dt_vencimento eh nulo).

Revision 1.24  2006/11/01 18:18:17  dibueno
Bug #7285

Revision 1.23  2006/10/30 13:24:16  dibueno
#7285#

Revision 1.22  2006/10/26 14:07:29  cercato
recuperando cod_convenio funcao montaRelacionamento,

Revision 1.21  2006/10/19 18:45:09  cercato
setando ano exercicio como complemento.

Revision 1.20  2006/10/03 17:53:50  dibueno
Adicionada descricao do credito

Revision 1.19  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.18  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.CREDITO_GRUPO
  * Data de Criação: 12/05/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Lucas Teixeira Stephanou

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRCreditoGrupo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRCreditoGrupo()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.credito_grupo');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_grupo,cod_credito,cod_especie,cod_genero,cod_natureza,ano_exercicio');
                  //nome,           tipo         requerido   Tamanho   PK     FK    Conteudo = ''
    $this->AddCampo('cod_grupo'     ,'integer'  ,true       ,''     ,false  ,true  	);
    $this->AddCampo('cod_credito'   ,'integer'  ,true       ,''     ,false  ,true  	);
    $this->AddCampo('cod_especie'   ,'integer'  ,true       ,''     ,false  ,true  	);
    $this->AddCampo('cod_genero'    ,'integer'  ,true       ,''     ,false  ,true  	);
    $this->AddCampo('cod_natureza'  ,'integer'  ,true       ,''     ,false  ,true  	);
    $this->AddCampo('desconto'      ,'boolean'  ,true       ,''     ,false  ,false 	);
    $this->AddCampo('ano_exercicio' ,'varchar'  ,true       ,'4'    ,true   ,true  	);
    $this->AddCampo('ordem'			,'integer'	,true		,''		,false	,false	);
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                        	\r\n";
    $stSql .= "    acg.cod_credito,                          	\r\n";
    $stSql .= "    acg.cod_grupo,                            	\r\n";
    $stSql .= "    acg.ano_exercicio,                        	\r\n";
    $stSql .= "    acg.cod_especie,                          	\r\n";
    $stSql .= "    acg.cod_genero,                           	\r\n";
    $stSql .= "    acg.cod_natureza,                         	\r\n";
    $stSql .= "    acg.desconto,                             	\r\n";
    $stSql .= "    acg.ordem,	                             	\r\n";
    $stSql .= "    mc.descricao_credito,                     	\r\n";
    $stSql .= "    mc.cod_convenio                           	\r\n";
    $stSql .= "FROM                                          	\r\n";
    $stSql .= "    arrecadacao.credito_grupo as acg         	\r\n";
    $stSql .= "    INNER JOIN monetario.credito mc ON           \r\n";
    $stSql .= "    acg.cod_credito = mc.cod_credito AND      	\r\n";
    $stSql .= "    acg.cod_especie = mc.cod_especie AND      	\r\n";
    $stSql .= "    acg.cod_genero  = mc.cod_genero  AND      	\r\n";
    $stSql .= "    acg.cod_natureza= mc.cod_natureza         	\r\n";

return $stSql;

}

function recuperaCreditoFuncao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCreditoFuncao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    //$this->debug(); //exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaCreditoFuncao()
{
    $stSql  = " SELECT
                    a.*,
                    d.valor_correspondente,
                    e.cod_funcao,
                    e.cod_modulo,
                    e.cod_biblioteca,
                    f.cod_convenio,
                    f.num_convenio,
                    g.cod_carteira,
                    d.valor_correspondente,
                    e.nom_funcao
                FROM
                (
                    SELECT  DISTINCT
                        a.cod_grupo,
                        a.ano_exercicio as exercicio,
                        c.cod_credito,
                        c.descricao_credito,
                        c.cod_especie,
                        c.cod_genero,
                        c.cod_natureza,
                        c.cod_convenio,
                        (
                            SELECT
                                MAX (timestamp)
                            FROM
                                arrecadacao.parametro_calculo AS d

                            WHERE
                                c.cod_credito       = d.cod_credito     AND
                                c.cod_especie       = d.cod_especie     AND
                                c.cod_genero        = d.cod_genero      AND
                                c.cod_natureza      = d.cod_natureza
                        ) AS INVIG

                    FROM
                        arrecadacao.grupo_credito a,
                        arrecadacao.credito_grupo b,
                        monetario.credito c

                    WHERE
                            a.cod_grupo         = b.cod_grupo       AND
                            b.cod_credito       = c.cod_credito     AND
                            b.cod_especie       = c.cod_especie     AND
                            b.cod_genero        = c.cod_genero      AND
                            b.cod_natureza      = c.cod_natureza
                )AS a,

                arrecadacao.parametro_calculo AS d,
                administracao.funcao e,
                monetario.convenio f
                left join
                    monetario.carteira g
                on
                    g.cod_convenio = f.cod_convenio

                WHERE
                    a.cod_credito       = d.cod_credito     AND
                    a.cod_especie       = d.cod_especie     AND
                    a.cod_genero        = d.cod_genero      AND
                    a.cod_natureza      = d.cod_natureza    AND
                    a.INVIG             = d.timestamp       AND
                    f.cod_convenio      = a.cod_convenio    AND
                    d.cod_funcao        = e.cod_funcao      AND
                    d.cod_modulo        = e.cod_modulo      AND
                    d.cod_biblioteca    = e.cod_biblioteca \n";

    return $stSql;
}

function recuperaCreditoEspecificoFuncao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCreditoFuncao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCreditoEspecificoFuncao()
{
    $stSql  = "SELECT                                                   \n";
    $stSql .= "    c.cod_credito,                                       \n";
    $stSql .= "    c.cod_especie,                                       \n";
    $stSql .= "    c.cod_genero,                                        \n";
    $stSql .= "    c.cod_natureza,                                      \n";
    $stSql .= "    e.cod_funcao,                                        \n";
    $stSql .= "    e.cod_modulo,                                        \n";
    $stSql .= "    e.cod_biblioteca,                                    \n";
    $stSql .= "    e.nom_funcao                                         \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    monetario.credito c,                                 \n";
    $stSql .= "    arrecadacao.parametro_calculo d,                     \n";
    $stSql .= "    administracao.funcao e                               \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "    c.cod_credito       = d.cod_credito     AND          \n";
    $stSql .= "    c.cod_especie       = d.cod_especie     AND          \n";
    $stSql .= "    c.cod_genero        = d.cod_genero      AND          \n";
    $stSql .= "    c.cod_natureza      = d.cod_natureza    AND          \n";
    $stSql .= "                                                         \n";
    $stSql .= "    d.cod_funcao        = e.cod_funcao      AND          \n";
    $stSql .= "    d.cod_modulo        = e.cod_modulo      AND          \n";
    $stSql .= "    d.cod_biblioteca    = e.cod_biblioteca               \n";

    return $stSql;
}

}
?>
