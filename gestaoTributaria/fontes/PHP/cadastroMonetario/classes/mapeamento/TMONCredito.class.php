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
    * Classe de regra de negócio para MONETARIO.CREDITO
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONCredito.class.php 63344 2015-08-19 18:51:30Z arthur $

* Casos de uso: uc-05.05.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONCredito extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    $this->setTabela('monetario.credito');

    $this->setCampoCod('cod_credito');
    $this->setComplementoChave('cod_credito,cod_natureza,cod_genero,cod_especie');

    $this->AddCampo('cod_credito','INTEGER',true,'',true,false);
    $this->AddCampo('cod_natureza','INTEGER',true,'',true,true);
    $this->AddCampo('cod_genero','INTEGER',true,'',true,true);
    $this->AddCampo('descricao_credito','varchar',true,'80',false,false);
    $this->AddCampo('cod_especie','INTEGER',true,'',true,true);
    $this->AddCampo('cod_convenio','INTEGER',false,'',false,true);
}

public function recuperaPermissaoGrupo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaPermissaoGrupo().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

public function montaRecuperaPermissaoGrupo()
{
    $stSql  = "    SELECT                                           \r\n";
    $stSql .= "        mc.cod_credito   ,                           \r\n";
    $stSql .= "        mc.cod_especie   ,                           \r\n";
    $stSql .= "        mc.cod_genero    ,                           \r\n";
    $stSql .= "        mc.cod_natureza  ,                           \r\n";
    $stSql .= "        acg.cod_grupo    ,                           \r\n";
    $stSql .= "        ap.numcgm                                    \r\n";
    $stSql .= "    FROM                                             \r\n";
    $stSql .= "        monetario.credito           as mc   ,        \r\n";
    $stSql .= "        arrecadacao.credito_grupo   as acg  ,        \r\n";
    $stSql .= "        arrecadacao.grupo_credito   as agc  ,        \r\n";
    $stSql .= "        arrecadacao.permissao       as ap            \r\n";
    $stSql .= "    WHERE                                            \r\n";
    $stSql .= "        mc.cod_credito  = acg.cod_credito   AND      \r\n";
    $stSql .= "        acg.cod_grupo   = agc.cod_grupo     AND      \r\n";
    $stSql .= "        ap.cod_grupo    = agc.cod_grupo              \r\n";

    return $stSql;
}

public function recuperaMascaraCredito(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMascaraCredito().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaMascaraCredito()
{
    $stSql  = " SELECT                                                                    \r\n";
    $stSql .= "        trim(                                                              \r\n";
    $stSql .= "             trim(repeat('9',length(max(c.cod_credito   )::varchar ) ) )  || '.' || \r\n";
    $stSql .= "             trim(repeat('9',length(max(e.cod_especie   )::varchar ) ) )  || '.' || \r\n";
    $stSql .= "             trim(repeat('9',length(max(g.cod_genero    )::varchar ) ) )  || '.' || \r\n";
    $stSql .= "             trim(repeat('9',length(max(n.cod_natureza  )::varchar ) ) )            \r\n";
    $stSql .= "        ) as mascara_credito                                               \r\n";
    $stSql .= "    FROM                                                                   \r\n";
    $stSql .= "        monetario.natureza_credito as n,                                   \r\n";
    $stSql .= "        monetario.genero_credito as g ,                                    \r\n";
    $stSql .= "        monetario.especie_credito as e,                                    \r\n";
    $stSql .= "        monetario.credito as c;                                            \r\n";

    return $stSql;
}

public function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT DISTINCT                                                                                                                                  \r\n ";
    $stSql .= "     mc.cod_credito,                                                                                                                               \r\n ";
    $stSql .= "     mn.cod_natureza,                                                                                                                            \r\n ";
    $stSql .= "     mn.nom_natureza,                                                                                                                           \r\n ";
    $stSql .= "     mg.cod_genero,                                                                                                                              \r\n ";
    $stSql .= "     mg.nom_genero,                                                                                                                             \r\n ";
    $stSql .= "     me.cod_especie,                                                                                                                             \r\n ";
    $stSql .= "     me.nom_especie,                                                                                                                            \r\n ";
    $stSql .= "     mc.descricao_credito,                                                                                                                     \r\n ";
    $stSql .= "     mc.cod_convenio,                                                                                                                           \r\n ";
    $stSql .= "     mcc.cod_conta_corrente,
                    afdc.cod_modulo,
                    afdc.cod_funcao,
                    afdc.cod_biblioteca,
                    afdc.nom_funcao
    \n";
    $stSql .= " FROM                                                                                                                                                    \r\n ";
    $stSql .= "     monetario.credito as mc                                                                                                                  \r\n ";

    $stSql .= " LEFT JOIN
                    monetario.regra_desoneracao_credito AS mrdc
                ON
                    mrdc.cod_credito = mc.cod_credito
                    AND mrdc.cod_especie = mc.cod_especie
                    AND mrdc.cod_natureza = mc.cod_natureza
                    AND mrdc.cod_genero = mc.cod_genero
    ";

    $stSql .= " LEFT JOIN
                    administracao.funcao AS afdc
                ON
                    afdc.cod_modulo = mrdc.cod_modulo
                    AND afdc.cod_funcao = mrdc.cod_funcao
                    AND afdc.cod_biblioteca = mrdc.cod_biblioteca
    ";

    $stSql .= " LEFT JOIN \r\n ";
    $stSql .= "     monetario.credito_conta_corrente AS mcc  \r\n";
    $stSql .= " ON \r\n ";
    $stSql .= "     mcc.cod_credito = mc.cod_credito AND mcc.cod_genero = mc.cod_genero AND mcc.cod_natureza = mc.cod_natureza AND mcc.cod_especie = mc.cod_especie \r\n ";

    $stSql .= " INNER JOIN                                                                                                                                            \r\n ";
    $stSql .= "     monetario.especie_credito as me  ON mc.cod_natureza = me.cod_natureza                                   \r\n ";
    $stSql .= "     AND mc.cod_genero = me.cod_genero AND mc.cod_especie=me.cod_especie                               \r\n ";
    $stSql .= " INNER JOIN                                                                                                                                            \r\n ";
    $stSql .= "     monetario.genero_credito as mg ON me.cod_natureza = mg.cod_natureza                                    \r\n ";
    $stSql .= "     AND me.cod_genero = mg.cod_genero                                                                                            \r\n ";
    $stSql .= " INNER JOIN                                                                                                                                            \r\n ";
    $stSql .= "     monetario.natureza_credito as mn ON mg.cod_natureza = mn.cod_natureza                                  \r\n ";

return $stSql;
}

public function recuperaRelacionamentoGF(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoGF().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaRelacionamentoGF()
{
    $stSql ="SELECT DISTINCT
                   mccc.exercicio,
                   mccc.cod_entidade,
                   mc.cod_credito,
                   mn.cod_natureza,
                   mn.nom_natureza,
                   mg.cod_genero,
                   mg.nom_genero,
                   me.cod_especie,
                   me.nom_especie,
                   mc.descricao_credito,
                   mc.cod_convenio,
                   mcc.cod_conta_corrente
             FROM
                   monetario.credito as mc
             LEFT JOIN monetario.credito_conta_corrente  AS mcc
                   ON  mcc.cod_credito = mc.cod_credito  AND
                   mcc.cod_genero = mc.cod_genero        AND
                   mcc.cod_natureza = mc.cod_natureza    AND
                   mcc.cod_especie = mc.cod_especie

             JOIN monetario.especie_credito              AS me
                   ON mc.cod_natureza = me.cod_natureza  AND
                      mc.cod_genero = me.cod_genero      AND
                      mc.cod_especie=me.cod_especie

             JOIN  monetario.genero_credito              AS mg
                   ON me.cod_natureza = mg.cod_natureza AND
                      me.cod_genero = mg.cod_genero

             JOIN  monetario.natureza_credito            AS mn
                   ON mg.cod_natureza = mn.cod_natureza

            JOIN  ( SELECT
                                cod_credito
                               ,cod_especie
                               ,cod_genero
                               ,cod_natureza
                               ,plano_banco.exercicio
                               ,cod_entidade
                         FROM
                                contabilidade.plano_banco
                         JOIN  monetario.credito_conta_corrente ON
                                plano_banco.cod_banco = credito_conta_corrente.cod_banco AND
                                plano_banco.cod_agencia =  credito_conta_corrente.cod_agencia AND
                                plano_banco.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente

                              , contabilidade.plano_analitica
                              , contabilidade.plano_conta
                         WHERE
                                plano_banco.cod_plano = plano_analitica.cod_plano
                            and plano_conta.cod_conta = plano_analitica.cod_conta
                            and plano_analitica.cod_conta = plano_conta.cod_conta
                            and plano_analitica.exercicio = plano_conta.exercicio
                            and plano_analitica.cod_plano = plano_banco.cod_plano
                         GROUP BY 1,2,3,4,5,6
                         ORDER BY 1
                       ) mccc
                         ON ( mccc.cod_credito = mc.cod_credito  AND
                         mccc.cod_especie     = mc.cod_especie  AND
                         mccc.cod_genero      = mc.cod_genero   AND
                         mccc.cod_natureza    = mc.cod_natureza ) ";

return $stSql;
}

public function recuperaRelacionamentoUnico(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoUnico().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaRelacionamentoUnico()
{
    $stSql  = "     SELECT  DISTINCT                                                    \n";
    $stSql .= "         mc.cod_credito,                                     \n";
    $stSql .= "         mc.cod_convenio,                                   \n";
    $stSql .= "         mcar.cod_carteira,                                   \n";
    $stSql .= "         mn.cod_natureza,                                  \n";
    $stSql .= "         mn.nom_natureza,                                  \n";
    $stSql .= "         mg.cod_genero,                                    \n";
    $stSql .= "         mg.nom_genero,                                    \n";
    $stSql .= "         me.cod_especie,                                   \n";
    $stSql .= "         me.nom_especie,                                   \n";
    $stSql .= "         mc.descricao_credito,                             \n";
    $stSql .= "         admf.nom_funcao,                                  \n";
    $stSql .= "         apc.timestamp                                     \n";
    $stSql .= "     FROM                                                                       \n";
    $stSql .= "         monetario.credito as mc                                     \n";

    $stSql .= "    LEFT JOIN                                                                \n";
    $stSql .= "         monetario.carteira as mcar                               \n";
    $stSql .= "     ON                                                                          \n";
    $stSql .= "         mcar.cod_convenio = mc.cod_convenio             \n";

    $stSql .= "    INNER JOIN                                                                \n";
    $stSql .= "         arrecadacao.parametro_calculo as apc              \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         apc.cod_credito = mc.cod_credito and              \n";
    $stSql .= "         apc.cod_especie = mc.cod_especie and              \n";
    $stSql .= "         apc.cod_genero  = mc.cod_genero  and              \n";
    $stSql .= "         apc.cod_natureza = mc.cod_natureza                \n";
    $stSql .= "     LEFT JOIN                                             \n";
    $stSql .= "         administracao.funcao as admf                      \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         admf.cod_funcao = apc.cod_funcao and              \n";
    $stSql .= "         admf.cod_biblioteca = apc.cod_biblioteca and      \n";
    $stSql .= "         admf.cod_modulo = apc.cod_modulo                  \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.especie_credito as me                   \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         mc.cod_natureza = me.cod_natureza and             \n";
    $stSql .= "         mc.cod_genero = me.cod_genero and                 \n";
    $stSql .= "         mc.cod_especie = me.cod_especie                   \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.genero_credito as mg                    \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         me.cod_natureza = mg.cod_natureza and             \n";
    $stSql .= "         me.cod_genero = mg.cod_genero                     \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.natureza_credito as mn                  \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         mg.cod_natureza = mn.cod_natureza                 \n";

return $stSql;
}

public function recuperaRelacionamentoPopUp(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoPopUp().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaRelacionamentoPopUp()
{
    $stSql  = "     SELECT                                                \n";
    $stSql .= "         mc.cod_credito,                                   \n";
    $stSql .= "         mn.cod_natureza,                                  \n";
    $stSql .= "         mn.nom_natureza,                                  \n";
    $stSql .= "         mg.cod_genero,                                    \n";
    $stSql .= "         mg.nom_genero,                                    \n";
    $stSql .= "         me.cod_especie,                                   \n";
    $stSql .= "         me.nom_especie,                                   \n";
    $stSql .= "         mc.descricao_credito                              \n";
    $stSql .= "     FROM                                                  \n";
    $stSql .= "         monetario.credito as mc                           \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.especie_credito as me                   \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         mc.cod_natureza = me.cod_natureza and             \n";
    $stSql .= "         mc.cod_genero = me.cod_genero and                 \n";
    $stSql .= "         mc.cod_especie = me.cod_especie                   \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.genero_credito as mg                    \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         me.cod_natureza = mg.cod_natureza and             \n";
    $stSql .= "         me.cod_genero = mg.cod_genero                     \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.natureza_credito as mn                  \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         mg.cod_natureza = mn.cod_natureza                 \n";

return $stSql;
}

public function recuperaRelacionamentoPopUpGF(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoPopUpGF().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaRelacionamentoPopUpGF()
{
    $stSql  = "     SELECT                                                \n";
    $stSql .= "         mccc.exercicio,                                   \n";
    $stSql .= "         mccc.cod_entidade,                                \n";
    $stSql .= "         mc.cod_credito,                                   \n";
    $stSql .= "         mn.cod_natureza,                                  \n";
    $stSql .= "         mn.nom_natureza,                                  \n";
    $stSql .= "         mg.cod_genero,                                    \n";
    $stSql .= "         mg.nom_genero,                                    \n";
    $stSql .= "         me.cod_especie,                                   \n";
    $stSql .= "         me.nom_especie,                                   \n";
    $stSql .= "         mc.descricao_credito                              \n";
    $stSql .= "     FROM                                                  \n";
    $stSql .= "         monetario.credito as mc                           \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.especie_credito as me                   \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         mc.cod_natureza = me.cod_natureza and             \n";
    $stSql .= "         mc.cod_genero = me.cod_genero and                 \n";
    $stSql .= "         mc.cod_especie = me.cod_especie                   \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.genero_credito as mg                    \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         me.cod_natureza = mg.cod_natureza and             \n";
    $stSql .= "         me.cod_genero = mg.cod_genero                     \n";
    $stSql .= "     INNER JOIN                                            \n";
    $stSql .= "         monetario.natureza_credito as mn                  \n";
    $stSql .= "     ON                                                    \n";
    $stSql .= "         mg.cod_natureza = mn.cod_natureza                 \n";
    $stSql .= "    JOIN  ( SELECT
                                cod_credito
                               ,cod_especie
                               ,cod_genero
                               ,cod_natureza
                               ,plano_banco.exercicio
                               ,cod_entidade
                         FROM
                                contabilidade.plano_banco
                         JOIN  monetario.credito_conta_corrente ON
                                plano_banco.cod_banco = credito_conta_corrente.cod_banco AND
                                plano_banco.cod_agencia =  credito_conta_corrente.cod_agencia AND
                                plano_banco.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente

                              , contabilidade.plano_analitica
                              , contabilidade.plano_conta
                         WHERE
                                plano_banco.cod_plano = plano_analitica.cod_plano
                            and plano_conta.cod_conta = plano_analitica.cod_conta
                            and plano_analitica.cod_conta = plano_conta.cod_conta
                            and plano_analitica.exercicio = plano_conta.exercicio
                            and plano_analitica.cod_plano = plano_banco.cod_plano
                         GROUP BY 1,2,3,4,5,6
                         ORDER BY 1
                       ) mccc
                         ON ( mccc.cod_credito = mc.cod_credito  AND
                         mccc.cod_especie     = mc.cod_especie  AND
                         mccc.cod_genero      = mc.cod_genero   AND
                         mccc.cod_natureza    = mc.cod_natureza )

              ";

return $stSql;
}

}

?>