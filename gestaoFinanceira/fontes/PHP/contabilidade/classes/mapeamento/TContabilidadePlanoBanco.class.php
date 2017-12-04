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
    * Classe de mapeamento da tabela CONTABILIDADE.PLANO_BANCO
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32032 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.02.02,uc-02.04.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.PLANO_BANCO
  * Data de Criação: 01/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadePlanoBanco extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadePlanoBanco()
{
    parent::Persistente();
    $this->setTabela('contabilidade.plano_banco');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_plano,exercicio');

    $this->AddCampo('cod_plano','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_banco','varchar',true,'05',false,false);
    $this->AddCampo('cod_agencia','varchar',true,'10',false,false);
    $this->AddCampo('conta_corrente','varchar',true,'30',false,false);
    $this->AddCampo('cod_entidade', 'integer', true, '', false, false);
    $this->AddCampo('cod_conta_corrente','integer',true,'',false,false);

}
/**
*
*
*/
function montaRecuperaRelacionamento()
{
    $stSQL  = "SELECT pc.cod_estrutural, pa.cod_plano, pc.nom_conta,                                             \n ";
    $stSQL .= "       r.masc_recurso as cod_recurso, pa.natureza_saldo,         \n ";
    //$stSQL .= "       r.nom_recurso, pb.cod_banco, pb.cod_agencia, pb.conta_corrente, pb.cod_entidade          \n ";
    $stSQL .= "       pb.cod_banco, pb.cod_agencia, pb.conta_corrente, pb.cod_entidade            				 \n ";
    $stSQL .= "    FROM                                                                                          \n ";
    $stSQL .= "       contabilidade.plano_conta           as pc                                                  \n ";
    $stSQL .= "    INNER JOIN                                                                                    \n ";
    $stSQL .= "       contabilidade.plano_analitica       as pa                                                  \n ";
    $stSQL .= "    ON ( pc.exercicio = pa.exercicio and pc.cod_conta = pa.cod_conta )                            \n ";
    $stSQL .= "    LEFT JOIN                                                                                     \n ";
    $stSQL .= "       contabilidade.plano_banco           as pb                                                  \n ";
    $stSQL .= "    ON ( pa.exercicio = pb.exercicio and pa.cod_plano = pb.cod_plano )                            \n ";
    $stSQL .= "    INNER JOIN                                                                                    \n ";
    $stSQL .= "       contabilidade.plano_recurso         as pr                                                  \n ";
    $stSQL .= "    ON ( pa.exercicio = pr.exercicio and pa.cod_plano = pr.cod_plano )                            \n ";
    $stSQL .= "    INNER JOIN                                                                                    \n ";
    $stSQL .= "       orcamento.recurso as r																	 \n ";
    $stSQL .= "    ON ( pr.exercicio = r.exercicio and pr.cod_recurso = r.cod_recurso )                          \n ";

    return $stSQL;
}

function recuperaBancoDescricao(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $this->setDado( 'stFiltro', $stFiltro );
    $stSql = $this->montaRecuperaBancoDescricao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBancoDescricao()
{
    $stSQL  = "    SELECT                                                                \n";
    $stSQL  .= "         pa.cod_plano                                                    \n";
    $stSQL  .= "        ,pa.exercicio                                                    \n";
    $stSQL  .= "        ,pc.cod_estrutural                                               \n";
    $stSQL  .= "        ,pc.nom_conta                                                    \n";
    $stSQL  .= "        ,pc.cod_conta                                                    \n";
    $stSQL  .= "        , publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido  \n";
    $stSQL  .= "        , pc.cod_classificacao,pc.cod_sistema                            \n";
    $stSQL  .= "        , oe.cod_entidade                                               \n";
    $stSQL  .= "        , pa.natureza_saldo,                                    \n";
    $stSQL  .= "    CASE WHEN                                                            \n";
    $stSQL  .= "        publico.fn_nivel(cod_estrutural) > 4 THEN 5                      \n";
    $stSQL  .= "    ELSE                                                                 \n";
    $stSQL  .= "        publico.fn_nivel(cod_estrutural)                                 \n";
    $stSQL  .= "    END as nivel                                                         \n";
    $stSQL  .= "    FROM                                                                 \n";
    $stSQL  .= "        contabilidade.plano_conta as pc                                  \n";
    $stSQL  .= "       ,contabilidade.plano_analitica as pa                              \n";
    $stSQL  .= "       ,contabilidade.plano_banco as pb                                  \n";
    $stSQL  .= "       ,orcamento.entidade as oe                                         \n";
    $stSQL  .= "    WHERE                                                                \n";
    $stSQL  .= "        pc.exercicio = pa.exercicio                                      \n";
    $stSQL  .= "    AND pc.cod_conta = pa.cod_conta                                      \n";
    $stSQL  .= "    AND pa.exercicio = pb.exercicio                                      \n";
    $stSQL  .= "    AND pa.cod_plano = pb.cod_plano                                      \n";
    $stSQL  .= "    AND pb.exercicio = oe.exercicio                                      \n";
    $stSQL  .= "    AND pb.cod_entidade = oe.cod_entidade                                \n";
    $stSQL  .= $this->getDado( 'stFiltro' );
    $stSQL  .= "                                                                         \n";
    $stSQL  .= "    order by pc.cod_estrutural                                           \n";

    return $stSQL;
}

function recuperaBancoConciliacao(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $this->setDado( 'stFiltro', $stFiltro );
    $stSql = $this->montaRecuperaBancoConciliacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBancoConciliacao()
{
    $stSQL  = "    SELECT                                                                \n";
    $stSQL  .= "         pa.cod_plano                                                    \n";
    $stSQL  .= "        ,pa.exercicio                                                    \n";
    $stSQL  .= "        ,pc.cod_estrutural                                               \n";
    $stSQL  .= "        ,pc.nom_conta                                                    \n";
    $stSQL  .= "        ,pc.cod_conta                                                    \n";
    $stSQL  .= "        ,pa.natureza_saldo                                      \n";
    $stSQL  .= "        , publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido  \n";
    $stSQL  .= "        , pc.cod_classificacao,pc.cod_sistema                            \n";
    $stSQL  .= "        , oe.cod_entidade                                                \n";
    $stSQL  .= "        , cgm.nom_cgm                                                   \n";
    $stSQL  .= "        , pa.natureza_saldo,                                    \n";
    $stSQL  .= "    CASE WHEN                                                            \n";
    $stSQL  .= "        publico.fn_nivel(cod_estrutural) > 4 THEN 5                      \n";
    $stSQL  .= "    ELSE                                                                 \n";
    $stSQL  .= "        publico.fn_nivel(cod_estrutural)                                 \n";
    $stSQL  .= "    END as nivel                                                         \n";
    $stSQL  .= "    FROM                                                                 \n";
    $stSQL  .= "        contabilidade.plano_conta as pc                                  \n";
    $stSQL  .= "       ,contabilidade.plano_analitica as pa                              \n";
    $stSQL  .= "       ,contabilidade.plano_banco as pb                                  \n";
    $stSQL  .= "       ,orcamento.entidade as oe                                         \n";
    $stSQL  .= "       ,sw_cgm as cgm                                                    \n";
    $stSQL  .= "    WHERE                                                                \n";
    $stSQL  .= "        pc.exercicio = pa.exercicio                                      \n";
    $stSQL  .= "    AND pc.cod_conta = pa.cod_conta                                      \n";
    $stSQL  .= "    AND pa.exercicio = pb.exercicio                                      \n";
    $stSQL  .= "    AND pa.cod_plano = pb.cod_plano                                      \n";
    $stSQL  .= "    AND pb.exercicio = oe.exercicio                                      \n";
    $stSQL  .= "    AND pb.cod_entidade = oe.cod_entidade                                \n";
    $stSQL  .= "    AND oe.numcgm    = cgm.numcgm                                        \n";
    $stSQL  .= $this->getDado( 'stFiltro' );
    $stSQL  .= "                                                                         \n";
    $stSQL  .= "    order by pb.cod_plano                                                \n";

    return $stSQL;
}

function recuperaSaldoContaBanco(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoContaBanco().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoContaBanco()
{
    $stSql  = " SELECT contabilidade.fn_saldo_conta_banco( '".$this->getDado( 'exercicio' )."' \n";
    $stSql .= "                                           , ".$this->getDado( 'cod_plano' )."  \n";
    $stSql .= " ) as vl_saldo                                                                  \n";

    return $stSql;
}

function recuperaContaBanco(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaContaBanco().$stFiltro;
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaBanco()
{
        $stSql  = "SELECT 																				\n";
        $stSql .= "		cod_plano ,																		\n";
        $stSql .= "		exercicio ,																		\n";
        $stSql .= "		cod_banco ,																		\n";
        $stSql .= "		cod_agencia ,																	\n";
        $stSql .= "		conta_corrente ,																\n";
        $stSql .= "		cod_entidade 																	\n";
        $stSql .= "FROM 																				\n";
        $stSql .= "		contabilidade.plano_banco 														\n";
        $stSql .= "WHERE cod_banco is NOT NULL 															\n";
    if($this->getDado( 'exercicio' ))
        $stSql .= "		AND exercicio = '".$this->getDado( 'exercicio' )."'							\n";
    if($this->getDado( 'cod_plano' ))
        $stSql .= "		AND cod_plano = ".$this->getDado( 'cod_plano' )."								\n";
    if($this->getDado( 'cod_entidade' ))
        $stSql .= "		AND cod_entidade = ".$this->getDado( 'cod_entidade' )." 						\n";

        return $stSql;
}

function recuperaRelatorioContaBanco(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaRelatorioContaBanco",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaRelatorioContaBanco()
{
    $stSql = "
        SELECT  pc.cod_estrutural,
                pa.cod_plano,
                pc.nom_conta,
                r.cod_recurso,
                r.nom_recurso,
                mb.num_banco,
                ma.num_agencia,
                pb.conta_corrente,
                pb.cod_entidade,
                pa.natureza_saldo
                
          FROM contabilidade.plano_conta AS pc
          
          JOIN contabilidade.plano_analitica AS pa
            ON pc.exercicio = pa.exercicio
           AND pc.cod_conta = pa.cod_conta
           
          JOIN contabilidade.plano_banco AS pb
            ON pa.exercicio = pb.exercicio
           AND pa.cod_plano = pb.cod_plano
           
          JOIN monetario.banco AS mb
            ON mb.cod_banco = pb.cod_banco
            
          JOIN monetario.agencia AS ma
            ON ma.cod_banco = pb.cod_banco
           AND ma.cod_agencia = pb.cod_agencia
           
          JOIN contabilidade.plano_recurso AS pr
            ON pa.exercicio = pr.exercicio
           AND pa.cod_plano = pr.cod_plano
           
          JOIN orcamento.recurso as r
            ON pr.exercicio = r.exercicio
           AND pr.cod_recurso = r.cod_recurso
           
         WHERE pc.exercicio = '".$this->getDado('exercicio')."' \n";
         
        if ($this->getDado('entidades') != "") {
            $stSql .= " AND pb.cod_entidade IN (".$this->getDado('entidades').") \n";
        }
         
        if ($this->getDado('estruturalInicial') != "") {
            $stSql .= " AND pc.cod_estrutural BETWEEN '".$this->getDado('estruturalInicial')."' AND '".$this->getDado('estruturalFinal')."'  \n";
        }
         
        if ($this->getDado('banco') != "") {
            $stSql .= " AND mb.cod_banco = ".$this->getDado('banco')."  \n";
        }
        
        if ($this->getDado('agencia') != "") {
            $stSql .= " AND ma.cod_agencia = ".$this->getDado('agencia')."  \n";
        }
        
        if ($this->getDado('conta_corrente') != "") {
            $stSql .= " AND pb.cod_conta_corrente = ".$this->getDado('conta_corrente')."  \n";
        }
         
        if ($this->getDado('codPlanoInicial') != "") {
            $stSql .= " AND pa.cod_plano BETWEEN ".$this->getDado('codPlanoInicial')." AND ".$this->getDado('codPlanoFinal')."  \n";
        }
         
        if ($this->getDado('recurso') != "") {
            $stSql .= " AND r.cod_recurso = ".$this->getDado('recurso')." \n";
        }
        
        if ($this->getDado('descricao') != "") {
            $stSql .= " AND pc.nom_conta ilike ('%".$this->getDado('descricao')."%') \n";
        }
        
        if ($this->getDado('ordenacao') == 1) {
            $stSql .= " ORDER BY pc.cod_estrutural \n";
        } else if ($this->getDado('ordenacao') == 2) {
            $stSql .= " ORDER BY pa.cod_plano \n";
        } else if ($this->getDado('ordenacao') == 3) {
            $stSql .= " ORDER BY r.cod_recurso \n";
        } else if ($this->getDado('ordenacao') == 4) {
            $stSql .= " ORDER BY mb.cod_banco, ma.cod_agencia, pb.cod_conta_corrente \n";
        }
        
    return $stSql;
}

function listarPorEstrutural(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
        return $this->executaRecupera("montaListarPorEstrutural",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaListarPorEstrutural()
{
    $stSql = "
            SELECT plano_analitica.exercicio
                  ,plano_analitica.cod_conta
                  ,plano_analitica.cod_plano
                  ,plano_banco.conta_corrente
                  ,plano_banco.cod_entidade
                  ,plano_banco.cod_banco
                  ,plano_banco.cod_agencia
                  ,plano_banco.cod_conta_corrente
                  ,plano_conta.nom_conta
                  ,plano_conta.cod_classificacao
                  ,plano_conta.cod_sistema
                  ,plano_conta.cod_estrutural
                  ,plano_analitica.natureza_saldo
              FROM contabilidade.plano_banco
              JOIN contabilidade.plano_analitica USING (exercicio,cod_plano)
              JOIN contabilidade.plano_conta USING (exercicio,cod_conta)
             WHERE 1 = 1 ";
    if($this->getDado('exercicio'))
    $stSql.= " AND exercicio = '".$this->getDado('exercicio')."' 			\n";

    if($this->getDado('cod_plano'))
    $stSql.= " AND cod_plano = ".$this->getDado('cod_plano')."  			\n";

    if($this->getDado('conta_corrente'))
    $stSql.= " AND conta_corrente = '".$this->getDado('conta_corrente')."'  \n";

    if($this->getDado('cod_entidade'))
    $stSql.= " AND cod_entidade = '".$this->getDado('cod_entidade')."'  	\n";

    if($this->getDado('cod_banco'))
    $stSql.= " AND cod_banco = '".$this->getDado('cod_banco')."'  			\n";

    if($this->getDado('cod_agencia'))
    $stSql.= " AND cod_agencia = '".$this->getDado('cod_agencia')."'  		\n";

    return $stSql;
}

function getProximoEstruturalRecurso(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ( Sessao::getExercicio() > '2012' ) {
        $stSql  = "SELECT SUBSTR( MAX(cod_estrutural), 11, 2) AS prox_cod_estrutural ";
    } else {
        $stSql  = "SELECT SUBSTR( MAX(cod_estrutural), 17, 2) AS prox_cod_estrutural ";
    }
    $stSql .= "  FROM contabilidade.plano_conta ";
    $stSql .= " WHERE exercicio = '".Sessao::getExercicio()."' ";
    $stSql .= "   AND cod_estrutural like '".$this->getDado('cod_estrutural')."%' ";
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function countContasContabeisRecursoCredor(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaCountContasContabeisRecursoCredor();
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaCountContasContabeisRecursoCredor()
{
    $stSql  = "  SELECT COUNT(*) AS num_contas                              \n";
    $stSql .= "    FROM contabilidade.plano_conta                           \n";
    $stSql .= "    JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "      ON plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "     AND plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "    JOIN contabilidade.plano_recurso                         \n";
    $stSql .= "      ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "     AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    $stSql .= "   WHERE plano_conta.cod_estrutural LIKE '1.9.3.2.0.00.00.%' \n";
    $stSql .= "     AND plano_conta.cod_classificacao = 1                   \n";
    $stSql .= "     AND plano_conta.cod_sistema = 4                         \n";
    $stSql .= "     AND plano_conta.exercicio = '".$this->getDado('exercicio')."' \n";

    return $stSql;
}

function countContasContabeisRecursoDevedor(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaCountContasContabeisRecursoDevedor();
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaCountContasContabeisRecursoDevedor()
{
    $stSql  = "  SELECT COUNT(*) AS num_contas                              \n";
    $stSql .= "    FROM contabilidade.plano_conta                           \n";
    $stSql .= "    JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "      ON plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "     AND plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "    JOIN contabilidade.plano_recurso                         \n";
    $stSql .= "      ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "     AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    $stSql .= "   WHERE plano_conta.cod_estrutural LIKE '1.9.3.2.0.00.00.%' \n";
    $stSql .= "     AND plano_conta.cod_classificacao = 1                   \n";
    $stSql .= "     AND plano_conta.cod_sistema = 4                         \n";
    $stSql .= "     AND plano_conta.exercicio = '".$this->getDado('exercicio')."' \n";

    return $stSql;
}

function getContasRecurso(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaContasRecurso($boTransacao);
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaContasRecurso($boTransacao = "")
{
    $stSql  = "SELECT ( SELECT plano_analitica.cod_plano                           \n";
    $stSql .= "           FROM contabilidade.plano_conta                           \n";
    $stSql .= "           JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "             ON plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "            AND plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "           JOIN contabilidade.plano_recurso                         \n";
    $stSql .= "             ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "            AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    if ( Sessao::getExercicio() > '2012' ) {
        $stSql .= "          WHERE plano_conta.cod_estrutural LIKE '7.2.1.1.1.%' \n";
    } else {
        $stSql .= "          WHERE plano_conta.cod_estrutural LIKE '1.9.3.2.0.00.00.%' \n";
    }
    $stSql .= "            AND plano_recurso.cod_recurso = ".$this->getDado('cod_recurso')." \n";
    $stSql .= "            AND plano_conta.exercicio = '".$this->getDado('exercicio')."' \n";
    $stSql .= "       ) as cod_plano_um ,                                          \n";
    $stSql .= "      ( SELECT plano_analitica.cod_plano                           \n";
    $stSql .= "           FROM contabilidade.plano_conta                           \n";
    $stSql .= "           JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "             ON plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "            AND plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "           JOIN contabilidade.plano_recurso                         \n";
    $stSql .= "             ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "            AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    if ( Sessao::getExercicio() > '2012' ) {
        $stSql .= "          WHERE plano_conta.cod_estrutural LIKE '8.2.1.1.1.%' \n";
    } else {
        $stSql .= "          WHERE plano_conta.cod_estrutural LIKE '2.9.3.2.0.00.00.%' \n";
    }
    $stSql .= "            AND plano_recurso.cod_recurso = ".$this->getDado('cod_recurso')." \n";
    $stSql .= "            AND plano_conta.exercicio = '".$this->getDado('exercicio')."' \n";
    $stSql .= "       ) as cod_plano_dois                                          \n";

    return $stSql;
}

function getContasRecursoPagamentoTCEMS(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaContasRecursoPagamentoTCEMS($boTransacao);
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaContasRecursoPagamentoTCEMS()
{
    $stSql  = "SELECT ( SELECT plano_analitica.cod_plano                           \n";
    $stSql .= "           FROM contabilidade.plano_conta                           \n";
    $stSql .= "           JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "             ON plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "            AND plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "           JOIN contabilidade.plano_recurso                         \n";
    $stSql .= "             ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "            AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    $stSql .= "          WHERE plano_conta.cod_estrutural LIKE '8.2.1.1.4.%' \n";
    $stSql .= "            AND plano_recurso.cod_recurso = ".$this->getDado('cod_recurso')." \n";
    $stSql .= "            AND plano_conta.exercicio = '".$this->getDado('exercicio')."' \n";
    $stSql .= "       ) as cod_plano_um ,                                          \n";
    $stSql .= "      ( SELECT plano_analitica.cod_plano                           \n";
    $stSql .= "           FROM contabilidade.plano_conta                           \n";
    $stSql .= "           JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "             ON plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "            AND plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "           JOIN contabilidade.plano_recurso                         \n";
    $stSql .= "             ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "            AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    $stSql .= "          WHERE plano_conta.cod_estrutural LIKE '8.2.1.1.3.%' \n";
    $stSql .= "            AND plano_recurso.cod_recurso = ".$this->getDado('cod_recurso')." \n";
    $stSql .= "            AND plano_conta.exercicio = '".$this->getDado('exercicio')."' \n";
    $stSql .= "       ) as cod_plano_dois                                          \n";

    return $stSql;
}

function testaRecursoPagamentoTCEMS(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaTestaRecursoPagamentoTCEMS();
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaTestaRecursoPagamentoTCEMS()
{
    $stSql = "SELECT plano_analitica.cod_plano                           
                FROM contabilidade.plano_conta                           
                JOIN contabilidade.plano_analitica                       
                  ON plano_analitica.cod_conta = plano_conta.cod_conta   
                 AND plano_analitica.exercicio = plano_conta.exercicio   
                JOIN contabilidade.plano_recurso                         
                  ON plano_recurso.cod_plano = plano_analitica.cod_plano 
                 AND plano_recurso.exercicio = plano_analitica.exercicio 
               WHERE plano_conta.cod_estrutural LIKE '".$this->getDado('estrutural_teste')."'
                 AND plano_recurso.cod_recurso = ".$this->getDado('cod_recurso')."
                 AND plano_conta.exercicio = '".$this->getDado('exercicio')."'";
    return $stSql;
}

function getRecursoVinculoConta(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecursoVinculoConta();
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecursoVinculoConta()
{
    $stSql  = "  SELECT cod_recurso                              \n";
    $stSql .= "    FROM contabilidade.plano_recurso              \n";
    $stSql .= "   WHERE plano_recurso.exercicio = '".$this->getDado('exercicio')."' \n";
    $stSql .= "     AND plano_recurso.cod_plano = ".$this->getDado('cod_plano')." \n";

    return $stSql;
}

function recuperaSaldoInicialRecurso(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoInicialRecurso().$stFiltro;
    $this->setDebug ($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoInicialRecurso()
{
    $stSql  ="     SELECT                                                                       \n";
    $stSql .="                 sum(vl_lancamento) as saldo                                      \n";
    $stSql .="                ,contas.cod_recurso                                               \n";
    $stSql .="     FROM (                                                                       \n";
    $stSql .="                SELECT                                                            \n";
    $stSql .="                    plano_conta.cod_conta                                         \n";
    $stSql .="                    ,plano_conta.exercicio                                        \n";
    $stSql .="                    ,plano_conta.nom_conta                                        \n";
    $stSql .="                    ,plano_conta.cod_estrutural                                   \n";
    $stSql .="                    ,CPA.cod_plano                                                \n";
    $stSql .="                    ,CVL.tipo                                                     \n";
    $stSql .="                    ,CVL.tipo_valor                                               \n";
    $stSql .="                    ,CVL.vl_lancamento                                            \n";
    $stSql .="                    ,CVL.sequencia                                                \n";
    $stSql .="                    ,CPR.cod_recurso                                              \n";
    $stSql .="                        FROM contabilidade.plano_conta                            \n";
    $stSql .="                        ,contabilidade.plano_banco      AS CPB                    \n";
    $stSql .="                            ,contabilidade.plano_analitica  AS CPA                \n";
    $stSql .="                            ,contabilidade.conta_debito     AS CCD                \n";
    $stSql .="                            ,contabilidade.valor_lancamento AS CVL                \n";
    $stSql .="                            ,contabilidade.plano_recurso    AS CPR                \n";
    $stSql .="                          -- Join com plano_analitica                             \n";
    $stSql .="                        WHERE CPB.exercicio    = CPA.exercicio                    \n";
    $stSql .="                          AND CPB.cod_plano    = CPA.cod_plano                    \n";
    $stSql .="                      -- join com plano_conta                                     \n";
    $stSql .="                      AND plano_conta.cod_conta = CPA.cod_conta                   \n";
    $stSql .="                      AND plano_conta.exercicio = CPA.exercicio                   \n";
    $stSql .="                          -- Join com conta_debito                                \n";
    $stSql .="                          AND CPA.exercicio    = CCD.exercicio                    \n";
    $stSql .="                          AND CPA.cod_plano    = CCD.cod_plano                    \n";
    $stSql .="                          -- Join com valor_lacamento                             \n";
    $stSql .="                          AND CCD.exercicio    = CVL.exercicio                    \n";
    $stSql .="                          AND CCD.cod_entidade = CVL.cod_entidade                 \n";
    $stSql .="                          AND CCD.tipo         = CVL.tipo                         \n";
    $stSql .="                          AND CCD.tipo_valor   = CVL.tipo_valor                   \n";
    $stSql .="                          AND CCD.cod_lote     = CVL.cod_lote                     \n";
    $stSql .="                          AND CCD.sequencia    = CVL.sequencia                    \n";
    $stSql .="                          AND CPR.cod_plano    = CPA.cod_plano                    \n";
    $stSql .="                          AND CPR.exercicio    = CPA.exercicio                    \n";
    $stSql .="                          -- Filtros                                              \n";
    $stSql .="                          AND CPA.exercicio    = '".$this->getDado('exercicio')."' \n";
    $stSql .="                      AND CVL.tipo = 'I'                                          \n";
    $stSql .="          UNION                                                                   \n";
    $stSql .="                SELECT                                                            \n";
    $stSql .="                     plano_conta.cod_conta                                        \n";
    $stSql .="                    ,plano_conta.exercicio                                        \n";
    $stSql .="                    ,plano_conta.nom_conta                                        \n";
    $stSql .="                    ,plano_conta.cod_estrutural                                   \n";
    $stSql .="                    ,CPA.cod_plano                                                \n";
    $stSql .="                    ,CVL.tipo                                                     \n";
    $stSql .="                    ,CVL.tipo_valor                                               \n";
    $stSql .="                    ,CVL.vl_lancamento                                            \n";
    $stSql .="                    ,CVL.vl_lancamento                                            \n";
    $stSql .="                    ,CPR.cod_recurso                                              \n";
    $stSql .="                        FROM contabilidade.plano_conta                            \n";
    $stSql .="                        ,contabilidade.plano_banco      AS CPB                    \n";
    $stSql .="                            ,contabilidade.plano_analitica  AS CPA                \n";
    $stSql .="                            ,contabilidade.conta_credito    AS CCC                \n";
    $stSql .="                            ,contabilidade.valor_lancamento AS CVL                \n";
    $stSql .="                            ,contabilidade.plano_recurso    AS CPR                \n";
    $stSql .="                          -- Join com plano_analitica                             \n";
    $stSql .="                        WHERE CPB.exercicio    = CPA.exercicio                    \n";
    $stSql .="                          AND CPB.cod_plano    = CPA.cod_plano                    \n";
    $stSql .="                      -- join com plano_conta                                     \n";
    $stSql .="                      AND plano_conta.cod_conta = CPA.cod_conta                   \n";
    $stSql .="                      AND plano_conta.exercicio = CPA.exercicio                   \n";
    $stSql .="                          -- Join com conta_debito                                \n";
    $stSql .="                          AND CPA.exercicio    = CCC.exercicio                    \n";
    $stSql .="                          AND CPA.cod_plano    = CCC.cod_plano                    \n";
    $stSql .="                          -- Join com valor_lacamento                             \n";
    $stSql .="                          AND CCC.exercicio    = CVL.exercicio                    \n";
    $stSql .="                          AND CCC.cod_entidade = CVL.cod_entidade                 \n";
    $stSql .="                          AND CCC.tipo         = CVL.tipo                         \n";
    $stSql .="                          AND CCC.tipo_valor   = CVL.tipo_valor                   \n";
    $stSql .="                          AND CCC.cod_lote     = CVL.cod_lote                     \n";
    $stSql .="                          AND CCC.sequencia    = CVL.sequencia                    \n";
    $stSql .="                          AND CPR.cod_plano    = CPA.cod_plano                    \n";
    $stSql .="                          AND CPR.exercicio    = CPA.exercicio                    \n";
    $stSql .="                          -- Filtros                                              \n";
    $stSql .="                          AND CPA.exercicio    = '".$this->getDado('exercicio')."' \n";
    $stSql .="                          AND CVL.tipo = 'I'                                      \n";
    $stSql .="                    )as contas                                                    \n";
    $stSql .="                    LEFT JOIN contabilidade.plano_recurso                         \n";
    $stSql .="                        ON(contas.cod_plano = plano_recurso.cod_plano             \n";
    $stSql .="                        AND contas.exercicio = plano_recurso.exercicio)           \n";
    $stSql .="                    LEFT JOIN orcamento.recurso                                   \n";
    $stSql .="                        ON (recurso.cod_recurso = plano_recurso.cod_recurso       \n";
    $stSql .="                        AND recurso.exercicio = plano_recurso.exercicio)          \n";
    $stSql .="                    GROUP BY contas.cod_recurso                                   \n";
    $stSql .="                    ORDER BY contas.cod_recurso                                   \n";

    return $stSql;

    }
    function verificaContasRecurso(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaVerificaContasRecurso($boTransacao);
        $this->setDebug ($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaVerificaContasRecurso($boTransacao = "")
    {
        $stSql  = "         SELECT plano_analitica.cod_plano                           \n";
        $stSql .= "           FROM contabilidade.plano_conta                           \n";
        $stSql .= "           JOIN contabilidade.plano_analitica                       \n";
        $stSql .= "             ON plano_analitica.cod_conta = plano_conta.cod_conta   \n";
        $stSql .= "            AND plano_analitica.exercicio = plano_conta.exercicio   \n";
        $stSql .= "           JOIN contabilidade.plano_recurso                         \n";
        $stSql .= "             ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
        $stSql .= "            AND plano_recurso.exercicio = plano_analitica.exercicio \n";
        $stSql .= "          WHERE plano_conta.cod_estrutural LIKE '".$this->getDado('cod_estrutural')."' \n";
        $stSql .= "            AND plano_recurso.cod_recurso = ".$this->getDado('cod_recurso')." \n";
        $stSql .= "            AND plano_conta.exercicio = '".$this->getDado('exercicio')."' \n";
        return $stSql;
    }

}
