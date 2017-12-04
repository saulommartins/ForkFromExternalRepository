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
  * Classe de mapeamento da tabela CONCURSO.EDITAL
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30566 $
  $Name$
  $Author: leandro.zis $
  $Date: 2007-09-05 15:52:01 -0300 (Qua, 05 Set 2007) $

  * Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONCURSO.EDITAL
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TConcursoEdital extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TConcursoEdital()
{
    ;
    parent::Persistente();
    $this->setTabela('concurso.edital');

    $this->setCampoCod('cod_edital');
    $this->setComplementoChave('');

    $this->AddCampo('cod_edital','integer',true,'',true,false);
    $this->AddCampo('cod_norma','integer',true,'',false,false);
    $this->AddCampo('dt_aplicacao','date',true,'',false,false);
    $this->AddCampo('dt_prorrogacao','date',true,'',false,false);
    $this->AddCampo('nota_minima','numeric',true,'10,02',false,false);
    $this->AddCampo('meses_validade','integer',true,'',false,false);
    $this->AddCampo('avalia_titulacao','boolean',true,'',false,false);
    $this->AddCampo('tipo_prova','char',true,'1',false,false);

}

function recuperaExercicio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    ;
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaExercicio().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    ;
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    ;
    $stSql  = "SELECT                                                                       \n";
    $stSql .= "     C.*,                                                                    \n";
    $stSql .= "     to_char(C.dt_aplicacao, 'dd/mm/yyyy') as dt_aplicacao,                  \n";
    $stSql .= "     to_char(dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogacao,                \n";
    $stSql .= "     N.nom_norma,to_char(N.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao,    \n";
    $stSql .= "     to_char(N.dt_publicacao, 'yyyy') as ano_publicacao,                     \n";
    $stSql .= "     to_char(tabela.dt_publicacao, 'dd/mm/yyyy') as dt_homologacao           \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "     concurso.edital as C                                              \n";
    $stSql .= "LEFT JOIN (                                                                  \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "          N2.dt_publicacao,                                                  \n";
    $stSql .= "          ch.cod_edital                                                      \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         concurso.homologacao as ch,                                     \n";
    $stSql .= "         normas.norma as N2                                                  \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         ch.cod_homologacao = N2.cod_norma                                   \n";
    $stSql .= "     ) as tabela                                                             \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "     C.cod_edital = tabela.cod_edital,                                       \n";
    $stSql .= "     normas.norma as N                                                       \n";
    $stSql .= "WHERE                                                                        \n";
    $stSql .= "     C.cod_edital = N.cod_norma                                              \n";

    return $stSql;

}

function recuperaRelacionamentoHomologados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    ;
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoHomologados().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoHomologados()
{
    ;

    $stSql = "
    SELECT C.*,to_char(C.dt_aplicacao, 'dd/mm/yyyy') as dt_aplicacao,to_char(dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogacao,N.nom_norma,to_char(N.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao,to_char(N.dt_publicacao, 'yyyy') as ano_publicacao
    FROM concurso.edital as C, normas.norma as N WHERE C.cod_edital = N.cod_norma AND
    C.cod_edital in (SELECT cod_edital from concurso.homologacao)\n";

    return $stSql;

}

function montaRecuperaExercicio()
{
    ;

$stSql .= " select distinct(N.exercicio) from normas.norma N \n";
$stSql .= " inner join concurso.edital C on C.cod_edital=N.cod_norma \n";
return $stSql;

}

function montaRecuperaNotasEdital()
{
    ;

$stSql .= "   SELECT t.media                                                                                                        \n";
$stSql .= "   FROM                                                                                                                  \n";
$stSql .= "     (SELECT cca.cod_candidato as cod_candidato,                                                                         \n";
$stSql .= "         CASE c.avalia_titulacao                                                                                         \n";
$stSql .= "             WHEN 't' THEN round((cca.nota_titulacao + cca.nota_prova)/2,2)                                              \n";
$stSql .= "             WHEN 'f' THEN  cca.nota_prova                                                                               \n";
$stSql .= "         END as media FROM concurso.edital c, concurso.concurso_candidato cc,                                          \n";
$stSql .= "         concurso.candidato cca                                                                                          \n";
$stSql .= "         WHERE c.cod_edital = cc.cod_edital and cc.cod_candidato = cca.cod_candidato AND cca.nota_prova notnull) as t,   \n";
$stSql .= "         concurso.concurso_candidato as ccc                                                                              \n";
return $stSql;

}

/*
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaNotasEdital(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    ;
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = ' order by ' . $stOrdem;
    $stSql = $this->montaRecuperaNotasEdital().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaConcursoEsfinge(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaConcursoEsfinge",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaConcursoEsfinge()
{
    $stSql = "
select edital.cod_edital
      ,1 as cod_tipo_edital
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_homologacao
      ,to_char(edital.dt_aplicacao + (edital.meses_validade||' month')::interval, 'dd/mm/yyyy') as dt_validade
      ,to_char(edital.dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogacao
from concurso.edital
join concurso.homologacao
using (cod_edital)
join normas.norma
  on homologacao.cod_homologacao = norma.cod_norma
where edital.dt_aplicacao between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
  and to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
    ";

    return $stSql;
}

function recuperaResultadoConcursoEsfinge(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaResultadoConcursoEsfinge",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaResultadoConcursoEsfinge()
{
    $stSql = "
select edital.cod_edital
      ,concurso_candidato.cod_candidato
      ,1 as cod_tipo_quadro
      ,concurso_cargo.cod_cargo
      ,to_char(norma_cargo.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao
      ,1 as cod_grupo
      ,1 as cod_referencia
      ,cargo_padrao.cod_padrao
      ,sw_cgm_pessoa_fisica.cpf
      ,sw_cgm.nom_cgm
      ,candidato.classificacao
      ,candidato.nota_titulacao
from concurso.edital
join concurso.concurso_candidato
using (cod_edital)
join concurso.candidato
using (cod_candidato)
join concurso.concurso_cargo
using (cod_edital)
join (select cargo_sub_divisao.cod_cargo
            ,max(norma.dt_publicacao) as dt_publicacao
  from pessoal.cargo_sub_divisao
      ,normas.norma
  where norma.cod_norma = cargo_sub_divisao.cod_norma
    and dt_publicacao > to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
  group by cargo_sub_divisao.cod_cargo ) as norma_cargo
  on concurso_cargo.cod_cargo = norma_cargo.cod_cargo
join (select cod_padrao, cod_cargo, max(timestamp) as timestamp
  from pessoal.cargo_padrao
  where timestamp > to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
  group by cod_padrao, cod_cargo) as cargo_padrao
  on cargo_padrao.cod_cargo = concurso_cargo.cod_cargo
join sw_cgm_pessoa_fisica
  on sw_cgm_pessoa_fisica.numcgm = candidato.numcgm
join sw_cgm
  on sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
where edital.dt_aplicacao between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
  and to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
    ";

    return $stSql;
}

}
