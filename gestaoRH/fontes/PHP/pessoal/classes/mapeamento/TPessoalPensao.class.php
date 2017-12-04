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
    * Classe de mapeamento da tabela pessoal.pensao
    * Data de Criação: 03/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30936 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.pensao
  * Data de Criação: 03/04/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Bruce Cruz de Sena

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalPensao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalPensao()
{
    parent::Persistente();
    $this->setTabela("pessoal.pensao");

    $this->setCampoCod('cod_pensao');
    $this->setComplementoChave('cod_pensao,timestamp');

    $this->AddCampo('cod_pensao','integer',true,'',true,false);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('observacao', 'char', false, 200, false, false, '');
    $this->AddCampo('cod_dependente','integer',true,'',false,true);
    $this->AddCampo('cod_servidor','integer',true,'',false,true);
    $this->AddCampo('tipo_pensao','char',true,'1',false,false);
    $this->AddCampo('dt_inclusao','date',true,'',false,false);
    $this->AddCampo('dt_limite','date',true,'',false,false);
    $this->AddCampo('percentual','numeric',false,'5,2',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSQL = '';
    $stSQL .= "select pensao.cod_pensao                                                         \n";
    $stSQL .= " , pensao.timestamp                                                              \n";
    $stSQL .= " , pensao.cod_dependente                                                         \n";
    $stSQL .= " , pensao.cod_servidor                                                           \n";
    $stSQL .= " , pensao.tipo_pensao                                                            \n";
    $stSQL .= " , to_char( pensao.dt_inclusao ,'dd/mm/yyyy') as dt_inclusao                     \n";
    $stSQL .= " , to_char( pensao.dt_limite   ,'dd/mm/yyyy') as dt_limite                       \n";
    $stSQL .= " , pensao.percentual                                                             \n";
    $stSQL .= " , pensao.observacao                                                             \n";
    $stSQL .= " , pv.valor                                                                      \n";
    $stSQL .= " , pf.cod_biblioteca                                                             \n";
    $stSQL .= " , pf.cod_modulo                                                                 \n";
    $stSQL .= " , pf.cod_funcao                                                                 \n";
    $stSQL .= " , funcao.nom_funcao                                                             \n";
    $stSQL .= " , pr.numcgm                                                                     \n";
    $stSQL .= " , pb.cod_agencia                                                                \n";
    $stSQL .= " , pb.cod_banco                                                                  \n";
    $stSQL .= " , pb.conta_corrente                                                             \n";
    $stSQL .= " , agencia.num_agencia                                                           \n";
    $stSQL .= " , banco.num_banco                                                               \n";
    $stSQL .= "from pessoal.pensao as pensao                                                    \n";
    $stSQL .= "inner join pessoal.pensao_banco     as pb                                        \n";
    $stSQL .= "    on (pb.cod_pensao = pensao.cod_pensao and pb.timestamp = pensao.timestamp)   \n";
    $stSQL .= "inner join (                                                                     \n";
    $stSQL .= "            select pensao.cod_pensao,                                            \n";
    $stSQL .= "                   max(pensao.timestamp) as ultimo ,                             \n";
    $stSQL .= "                   pensao.cod_pensao::varchar || to_char(max(pensao.timestamp), 'yyyy-mm-dd HH24:MI:SS') as codigo               \n";
    $stSQL .= "            from  pessoal.pensao                                                 \n";
    $stSQL .= "            group by pensao.cod_pensao) as maximos                               \n";
    $stSQL .= "    on (                                                                         \n";
    $stSQL .= "        (maximos.codigo = (    pensao.cod_pensao::varchar || to_char(pensao.timestamp, 'yyyy-mm-dd HH24:MI:SS')   ))             \n";
    $stSQL .= "       and (maximos.codigo not in (  select pensao.cod_pensao::varchar || to_char(timestamp, 'yyyy-mm-dd HH24:MI:SS') as cod_exc \n";
    $stSQL .= "                                    from pessoal.pensao_excluida ))              \n";
    $stSQL .= "       )                                                                         \n";
    $stSQL .= "inner join monetario.agencia                                                     \n";
    $stSQL .= "     on ( pb.cod_agencia = agencia.cod_agencia                                   \n";
    $stSQL .= "      and pb.cod_banco   = agencia.cod_banco   )                                 \n";
    $stSQL .= "inner join monetario.banco                                                       \n";
    $stSQL .= "    on  (agencia.cod_banco = banco.cod_banco)                                    \n";
    $stSQL .= "left join pessoal.pensao_valor      as pv                                        \n";
    $stSQL .= "    on (pv.cod_pensao = pensao.cod_pensao and pv.timestamp = pensao.timestamp)   \n";
    $stSQL .= "left join pessoal.pensao_funcao     as pf                                        \n";
    $stSQL .= "    on(pf.cod_pensao = pensao.cod_pensao and pf.timestamp = pensao.timestamp)    \n";
    $stSQL .= "left join administracao.funcao                                                   \n";
    $stSQL .= "    on ( funcao.cod_biblioteca = pf.cod_biblioteca                               \n";
    $stSQL .= "        and funcao.cod_modulo     = pf.cod_modulo                                \n";
    $stSQL .= "        and funcao.cod_funcao     = pf.cod_funcao )                              \n";
    $stSQL .= "left join pessoal.responsavel_legal as pr                                        \n";
    $stSQL .= "    on (pr.cod_pensao = pensao.cod_pensao and pr.timestamp = pensao.timestamp)   \n";

    return $stSQL;
}

/*
 * Lista os contratos de dependentes para ser usado em IFiltroComponenteDependente
 */
function recuperaContratoDependentePensao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratoDependentePensao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratoDependentePensao()
{
    $stSQL = '';
    $stSQL .= "SELECT contrato.cod_contrato                                                                         \n";
    $stSQL .= "FROM pessoal.contrato,                                                                               \n";
    $stSQL .= "     pessoal.servidor_contrato_servidor,                                                             \n";
    $stSQL .= "     pessoal.servidor,                                                                               \n";
    $stSQL .= "     pessoal.pensao JOIN (                                                                           \n";
    $stSQL .= "                            SELECT cod_pensao,                                                       \n";
    $stSQL .= "                                   MAX(timestamp) AS timestamp                                       \n";
    $stSQL .= "                              FROM pessoal.pensao                                                    \n";
    $stSQL .= "                          GROUP BY cod_pensao                                                        \n";
    $stSQL .= "                         ) AS max_pensao                                                             \n";
    $stSQL .= "                    ON ( max_pensao.cod_pensao = pensao.cod_pensao AND                               \n";
    $stSQL .= "                         max_pensao.timestamp  = pensao.timestamp AND                                \n";
    $stSQL .= "                         pensao.cod_pensao || pensao.timestamp NOT IN (SELECT cod_pensao || timestamp\n";
    $stSQL .= "                                                                         FROM pessoal.pensao_excluida)\n";
    $stSQL .= "                       ),                                                                           \n";
    $stSQL .= "     pessoal.dependente                                                                             \n";
    $stSQL .= "WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato AND                           \n";
    $stSQL .= "      servidor_contrato_servidor.cod_servidor = servidor.cod_servidor AND                           \n";
    $stSQL .= "      servidor.cod_servidor = pensao.cod_servidor AND                                               \n";
    $stSQL .= "      pensao.cod_dependente = dependente.cod_dependente AND                                         \n";
    $stSQL .= "      dependente.cod_dependente NOT IN (                                                            \n";
    $stSQL .= "                                          SELECT cod_dependente                                     \n";
    $stSQL .= "                                            FROM pessoal.dependente_excluido                        \n";
    $stSQL .= "                                        GROUP BY cod_dependente                                     \n";
    $stSQL .= "                                       )                                                            \n";

    return $stSQL;
}

function recuperaDependentePensaoRemessaBanPara(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    if (trim($stOrdem)=="") {$stOrdem="ORDER BY nom_cgm";}
    $obErro = $this->executaRecupera("montaRecuperaDependentePensaoRemessaBanPara",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaDependentePensaoRemessaBanPara()
{
    $stSql .= "     SELECT *\n";
    $stSql .= "       FROM (\n";
    $stSql .= "            SELECT CASE WHEN responsavel_legal.numcgm IS NULL THEN\n";
    $stSql .= "                     (SELECT UPPER(nom_cgm) FROM sw_cgm WHERE numcgm = dependente.numcgm)\n";
    $stSql .= "                   ELSE\n";
    $stSql .= "                     (SELECT UPPER(nom_cgm) FROM sw_cgm WHERE numcgm = responsavel_legal.numcgm)\n";
    $stSql .= "                   END as nom_cgm\n";
    $stSql .= "                 , CASE WHEN responsavel_legal.numcgm IS NULL THEN  \n";
    $stSql .= "                     (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE numcgm = dependente.numcgm)\n";
    $stSql .= "                   ELSE\n";
    $stSql .= "                     (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE numcgm = responsavel_legal.numcgm)\n";
    $stSql .= "                   END as cpf\n";
    $stSql .= "                 , dependente.numcgm as numcgm_dependente\n";
    $stSql .= "                 , responsavel_legal.numcgm as numcgm_responsavel_legal\n";
    $stSql .= "                 , sw_cgm.numcgm\n";
    $stSql .= "                 , UPPER(sw_cgm.bairro) as bairro\n";
    $stSql .= "                 , sw_cgm.cep\n";
    $stSql .= "                 , sw_cgm.cod_municipio\n";
    $stSql .= "                 , sw_cgm.cod_pais\n";
    $stSql .= "                 , sw_cgm.cod_uf\n";
    $stSql .= "                 , UPPER(sw_cgm.complemento) as complemento\n";
    $stSql .= "                 , sw_cgm.dt_cadastro\n";
    $stSql .= "                 , sw_cgm.fone_residencial\n";
    $stSql .= "                 , UPPER(sw_cgm.logradouro) as logradouro\n";
    $stSql .= "                 , UPPER(sw_cgm.numero) as numero\n";
    $stSql .= "                 , sw_cgm_pessoa_fisica.cod_escolaridade\n";
    $stSql .= "                 , sw_cgm_pessoa_fisica.cod_uf_orgao_emissor\n";
    $stSql .= "                 , sw_cgm_pessoa_fisica.dt_nascimento\n";
    $stSql .= "                 , UPPER(sw_cgm_pessoa_fisica.orgao_emissor) as orgao_emissor\n";
    $stSql .= "                 , sw_cgm_pessoa_fisica.rg\n";
    $stSql .= "                 , UPPER(sw_cgm_pessoa_fisica.sexo) as sexo\n";
    $stSql .= "                 , ( SELECT UPPER(nom_municipio) as nom_municipio FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio and cod_uf = sw_cgm.cod_uf ) as cidade\n";
    $stSql .= "                 , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm.cod_uf and cod_pais = sw_cgm.cod_pais ) as uf\n";
    $stSql .= "                 , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm_pessoa_fisica.cod_uf_orgao_emissor and cod_pais = sw_cgm.cod_pais ) as uf_orgao_emissor\n";
    $stSql .= "                 , contrato_servidor.registro\n";
    $stSql .= "                 , contrato_servidor.cod_contrato\n";
    $stSql .= "                 , contrato_servidor.cod_servidor\n";
    $stSql .= "                 , contrato_servidor.cod_orgao\n";
    $stSql .= "                 , contrato_servidor.cod_local\n";
    $stSql .= "                 , banco.cod_banco\n";
    $stSql .= "                 , banco.num_banco\n";
    $stSql .= "                 , agencia.cod_agencia\n";
    $stSql .= "                 , agencia.num_agencia\n";
    $stSql .= "                 , pensao_banco.conta_corrente\n";
    $stSql .= "                 , dependente.cod_dependente\n";
    $stSql .= "             FROM ( SELECT registro\n";
    $stSql .= "                         , cod_contrato\n";
    $stSql .= "                         , cod_orgao\n";
    $stSql .= "                         , cod_local\n";
    $stSql .= "                         , cod_servidor\n";
    $stSql .= "                      FROM recuperarContratoServidor('l,o', '".Sessao::getEntidade()."', ".($this->getDado('inCodPeriodoMovimentacao')?$this->getDado('inCodPeriodoMovimentacao'):0).", 'geral', '', '".Sessao::getExercicio()."')\n";
    $stSql .= "                  ) as contrato_servidor\n";
    $stSql .= "       INNER JOIN pessoal.servidor_dependente\n";
    $stSql .= "               ON servidor_dependente.cod_servidor = contrato_servidor.cod_servidor\n";
    $stSql .= "       INNER JOIN pessoal.dependente\n";
    $stSql .= "               ON dependente.cod_dependente = servidor_dependente.cod_dependente\n";
    $stSql .= "       INNER JOIN ( SELECT pensao.*\n";
    $stSql .= "                      FROM pessoal.pensao\n";
    $stSql .= "                         , (  SELECT cod_pensao\n";
    $stSql .= "                                   , MAX(timestamp) AS timestamp\n";
    $stSql .= "                                FROM pessoal.pensao\n";
    $stSql .= "                               WHERE pensao.timestamp <= ultimoTimestampPeriodoMovimentacao(".($this->getDado('inCodPeriodoMovimentacao')?$this->getDado('inCodPeriodoMovimentacao'):0).",'".Sessao::getEntidade()."')\n";
    $stSql .= "                                 AND NOT EXISTS (SELECT 1\n";
    $stSql .= "                                                   FROM pessoal.pensao_excluida\n";
    $stSql .= "                                                  WHERE pensao_excluida.cod_pensao = pensao.cod_pensao\n";
    $stSql .= "                                                    AND pensao_excluida.timestamp = pensao.timestamp)\n";
    $stSql .= "                            GROUP BY cod_pensao) AS max_pensao\n";
    $stSql .= "                     WHERE pensao.cod_pensao = max_pensao.cod_pensao\n";
    $stSql .= "                       AND pensao.timestamp = max_pensao.timestamp\n";
    $stSql .= "                  ) as pensao\n";
    $stSql .= "               ON dependente.cod_dependente = pensao.cod_dependente\n";
    $stSql .= "       INNER JOIN pessoal.pensao_banco\n";
    $stSql .= "               ON pensao_banco.cod_pensao = pensao.cod_pensao\n";
    $stSql .= "              AND pensao_banco.timestamp = pensao.timestamp\n";
    $stSql .= "       INNER JOIN monetario.banco\n";
    $stSql .= "               ON pensao_banco.cod_banco = banco.cod_banco\n";
    $stSql .= "       INNER JOIN monetario.agencia\n";
    $stSql .= "               ON pensao_banco.cod_agencia = agencia.cod_agencia\n";
    $stSql .= "              AND pensao_banco.cod_banco = agencia.cod_banco\n";
    $stSql .= "       INNER JOIN sw_cgm\n";
    $stSql .= "               ON dependente.numcgm = sw_cgm.numcgm\n";
    $stSql .= "        LEFT JOIN sw_cgm_pessoa_fisica\n";
    $stSql .= "               ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm\n";
    $stSql .= "        LEFT JOIN pessoal.responsavel_legal\n";
    $stSql .= "               ON responsavel_legal.cod_pensao = pensao.cod_pensao\n";
    $stSql .= "                 AND responsavel_legal.timestamp = pensao.timestamp\n";
    $stSql .= "            ) as contrato \n";

    return $stSql;
}

function recuperaServidorComPensaoJudicial(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaServidorComPensaoJudicial",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaServidorComPensaoJudicial()
{
    $stSql .= "SELECT contrato.registro                                                                         \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                    \n";
    $stSql .= "  FROM pessoal.servidor_contrato_servidor                              \n";
    $stSql .= "     , pessoal.contrato                                                \n";
    $stSql .= "     , pessoal.servidor                                                \n";
    $stSql .= "     , sw_cgm                                                                                    \n";
    $stSql .= " WHERE servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                           \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                           \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                           \n";
    $stSql .= "   AND EXISTS (SELECT 1                                                                          \n";
    $stSql .= "                 FROM pessoal.servidor_dependente                      \n";
    $stSql .= "                    , pessoal.pensao                                   \n";
    $stSql .= "                WHERE servidor_dependente.cod_servidor = servidor.cod_servidor                   \n";
    $stSql .= "                  AND servidor_dependente.cod_dependente = pensao.cod_dependente                 \n";
    $stSql .= "                  AND servidor_dependente.cod_servidor = pensao.cod_servidor                     \n";
    $stSql .= "                  AND NOT EXISTS (SELECT 1                                                       \n";
    $stSql .= "                                    FROM pessoal.pensao_excluida       \n";
    $stSql .= "                                   WHERE pensao_excluida.cod_pensao = pensao.cod_pensao          \n";
    $stSql .= "                                     AND pensao_excluida.timestamp = pensao.timestamp)           \n";
    $stSql .= "                  AND NOT EXISTS (SELECT 1                                                       \n";
    $stSql .= "                                    FROM pessoal.dependente_excluido   \n";
    $stSql .= "                                   WHERE dependente_excluido.cod_dependente = servidor_dependente.cod_dependente\n";
    $stSql .= "                                     AND dependente_excluido.cod_servidor = servidor_dependente.cod_servidor))\n";

    return $stSql;
}

}
