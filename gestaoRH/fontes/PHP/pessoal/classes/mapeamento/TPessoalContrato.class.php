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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContrato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContrato()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato');

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('');

    $this->AddCampo('cod_contrato','integer',true,''  ,true,false);
    $this->AddCampo('registro'    ,'integer',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
$stSql  = "   select                                              \n";
$stSql .= "       cs.*,                                           \n";
$stSql .= "       c.registro,                                    \n";
$stSql .= "       recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao , \n";
$stSql .= "       cso.cod_orgao as cod_orgao,                     \n";
$stSql .= "       o.descricao as lotacao                          \n";
$stSql .= "   from                                                \n";
$stSql .= "       pessoal.contrato_servidor as cs,                \n";
$stSql .= "       pessoal.contrato as c,                          \n";
$stSql .= "       pessoal.contrato_servidor_orgao as cso,         \n";
$stSql .= "       organograma.orgao as o                          \n";
$stSql .= "   where                                               \n";
$stSql .= "       c.cod_contrato = cso.cod_contrato               \n";
$stSql .= "       and cs.cod_contrato = cso.cod_contrato          \n";
$stSql .= "       and cso.cod_orgao = o.cod_orgao                 \n";

return $stSql;

}

function montaRecuperaDigito()
{
$stSql  = "   select                                                \n";
$stSql .= "       *                                                 \n";
$stSql .= "   FROM                                                  \n";
$stSql .= "     publico.fn_mod11('".$this->getDado('registro')."'); \n";
$stSql .= "                    \n";

return $stSql;

}

function recuperaDigito(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    //$stOrdem = ' order by ' . $stOrdem;

    $stSql = $this->montaRecuperaDigito();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaCgmDoRegistro(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.registro ";
    $stSql = $this->montaRecuperaCgmDoRegistro().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function recuperaServidorNaoPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaServidorNaoPensionista().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCgmDoRegistro()
{
    $stSql  = "SELECT * FROM (                                                             \n";
    $stSql .= "SELECT sw_cgm.numcgm                                                        \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                       \n";
    $stSql .= "     , contrato.*                                                           \n";
    $stSql .= "     , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato, 0, '".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "  FROM pessoal.contrato                                                     \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                   \n";
    $stSql .= "     , pessoal.servidor                                                     \n";
    $stSql .= "     , sw_cgm                                                               \n";
    $stSql .= " WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato      \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor      \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                      \n";
    $stSql .= "UNION                                                                       \n";
    $stSql .= "SELECT sw_cgm.numcgm                                                        \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                       \n";
    $stSql .= "     , contrato.*                                                           \n";
    $stSql .= "     , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato, 0, '".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "  FROM pessoal.contrato                                                     \n";
    $stSql .= "     , pessoal.contrato_pensionista                                         \n";
    $stSql .= "     , pessoal.pensionista                                                  \n";
    $stSql .= "     , sw_cgm                                                               \n";
    $stSql .= " WHERE contrato.cod_contrato = contrato_pensionista.cod_contrato            \n";
    $stSql .= "   AND contrato_pensionista.cod_pensionista = pensionista.cod_pensionista   \n";
    $stSql .= "   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente   \n";
    $stSql .= "   AND pensionista.numcgm = sw_cgm.numcgm                                   \n";
    $stSql .= "   ) as contrato WHERE registro is not null                                 \n";

    return $stSql;
}

function recuperaCgmDoRegistroServidor(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.registro ";
    $stSql = $this->montaRecuperaCgmDoRegistroServidor().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCgmDoRegistroServidor()
{
    $stSql  = "SELECT sw_cgm.numcgm                                                        \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                       \n";
    $stSql .= "     , contrato.*                                                           \n";
    $stSql .= "     , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato, 0, '".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "  FROM sw_cgm                                                               \n";
    $stSql .= "   	, pessoal.contrato                                                     \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                   \n";
    $stSql .= "     , pessoal.servidor                                                     \n";
    $stSql .= "     , pessoal.contrato_servidor                                            \n";
    $stSql .= " WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato      \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor      \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                      \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato.cod_contrato               \n";

    return $stSql;
}

function listar(&$rsLista)
{
    $obErro      = new Erro;
    $rsLista     = new RecordSet;

    if ( $this->getDado('cod_contrato') ) {
        $stFiltro  = " AND cod_contrato=".$this->getDado('cod_contrato');
    }
    if ( $this->getDado('registro') ) {
        $stFiltro .= " AND registro=".$this->getDado('registro');
    }
    $stFiltro = ( $stFiltro != "" ) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";

    $obErro = $this->recuperaTodos( $rsLista, $stFiltro );

    return $obErro;
}

function montaRecuperaServidorNaoPensionista()
{
$stSql  = "   SELECT                                                          													\n";
$stSql .= "       cs.cod_contrato		                              		  													\n";
$stSql .= "   FROM                                                    	      													\n";
$stSql .= "       pessoal.contrato_servidor cs                	      	      													\n";
$stSql .= "   JOIN pessoal.contrato c                          	      		  													\n";
$stSql .= "       ON cs.cod_contrato = c.cod_contrato                    		  												\n";
$stSql .= "	  	  AND cs.cod_contrato = (										  												\n";
$stSql .= "		  		SELECT cod_contrato FROM pessoal.contrato WHERE contrato.registro = ".$this->getDado("registro")."  	\n";
$stSql .= "	  	  )															  													\n";
$stSql .= "	  	  AND cs.cod_contrato NOT IN (							  		  												\n";
$stSql .= "	  	  		SELECT p.cod_contrato FROM pessoal.contrato_pensionista p	  											\n";
$stSql .= "	  	  ) 														  	      											\n";
return $stSql;
}

function recuperaAdmissaoNomeacaoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAdmissaoNomeacaoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAdmissaoNomeacaoEsfinge()
{
    $stSql = "
select norma.cod_norma
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
      ,to_char(norma.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao
      ,contrato.registro
      ,1 as cod_tipo_quadro
      ,contrato_servidor.cod_cargo
      ,to_char(cargo_criacao.timestamp, 'dd/mm/yyyy') as dt_criacao
      ,1 as cod_grupo
      ,1 as cod_referencia
      ,contrato_servidor_padrao.cod_padrao
      ,recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao
      ,1 as cod_tipo_edital
      ,to_char(contrato_servidor_nomeacao_posse.dt_posse, 'dd/mm/yyyy') as dt_posse
      ,to_char(contrato_servidor_nomeacao_posse.dt_nomeacao, 'dd/mm/yyyy') as dt_nomeacao
      ,padrao_padrao.valor
from pessoal.contrato_servidor
join normas.norma
  on norma.cod_norma = contrato_servidor.cod_norma
join pessoal.contrato
  on contrato_servidor.cod_contrato = contrato.cod_contrato
join (select cod_cargo, min(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
      group by cod_cargo ) as cargo_criacao
  on contrato_servidor.cod_cargo = cargo_criacao.cod_cargo
join (select cod_sub_divisao, cod_cargo, max(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
      group by cod_sub_divisao, cod_cargo ) as cargo_sub_divisao
  on cargo_sub_divisao.cod_cargo = contrato_servidor.cod_cargo
 and cargo_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
join (select contrato_servidor_padrao.cod_padrao, contrato_servidor_padrao.cod_contrato
        from pessoal.contrato_servidor_padrao
            ,(select cod_contrato
                    , max(timestamp) as timestamp
              from pessoal.contrato_servidor_padrao
              group by cod_contrato) as max_contrato_servidor_padrao
              where contrato_servidor_padrao.cod_contrato  = max_contrato_servidor_padrao.cod_contrato
              and contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao
  on contrato_servidor_padrao.cod_contrato = contrato.cod_contrato
join (select padrao_padrao.cod_padrao, padrao_padrao.valor
      from folhapagamento.padrao_padrao
          ,(select cod_padrao, max(timestamp) as timestamp
              from folhapagamento.padrao_padrao
            group by cod_padrao) as ultimo_padrao
      where padrao_padrao.cod_padrao = ultimo_padrao.cod_padrao
        and padrao_padrao.timestamp = ultimo_padrao.timestamp) as padrao_padrao
  on padrao_padrao.cod_padrao = contrato_servidor_padrao.cod_padrao
join (select contrato_servidor_orgao.cod_orgao, contrato_servidor_orgao.cod_contrato
      from pessoal.contrato_servidor_orgao
          ,(select cod_orgao, max(timestamp) as timestamp
              from pessoal.contrato_servidor_orgao
            group by cod_orgao) as ultimo_contrato_servidor_orgao
      where contrato_servidor_orgao.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao
        and contrato_servidor_orgao.timestamp = ultimo_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
  on contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
join organograma.orgao
  on orgao.cod_orgao = contrato_servidor_orgao.cod_orgao
join (select contrato_servidor_nomeacao_posse.cod_contrato
            ,contrato_servidor_nomeacao_posse.dt_posse
            ,contrato_servidor_nomeacao_posse.dt_nomeacao
      from pessoal.contrato_servidor_nomeacao_posse
          ,(select cod_contrato, max(timestamp) as timestamp
              from pessoal.contrato_servidor_nomeacao_posse
            group by cod_contrato) as ultimo_contrato_servidor_nomeacao_posse
      where contrato_servidor_nomeacao_posse.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato
        and contrato_servidor_nomeacao_posse.timestamp = ultimo_contrato_servidor_nomeacao_posse.timestamp) as contrato_servidor_nomeacao_posse
  on contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
where cargo_sub_divisao.cod_sub_divisao in (1,4,6)
  and norma.dt_assinatura between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
";

    return $stSql;
}

function recuperaContratacaoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaContratacaoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratacaoEsfinge()
{
    $stSql = "
select contrato_servidor.cod_norma
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
      ,to_char(norma.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao
      ,norma.descricao as desc_norma
      ,contrato_servidor.cod_cargo
      ,contrato.registro
      ,1 as cod_tipo_quadro
      ,contrato_servidor.cod_cargo
      ,to_char(cargo_criacao.timestamp, 'dd/mm/yyyy') as dt_criacao
      ,1 as cod_grupo
      ,1 as cod_referencia
      ,contrato_servidor_padrao.cod_padrao
      ,recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_orgao
      ,servidor_ctps.numero
      ,servidor_ctps.serie
      ,padrao_padrao.valor
from pessoal.contrato_servidor
join pessoal.contrato
  on contrato.cod_contrato = contrato_servidor.cod_contrato
join (select cod_cargo, min(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
      group by cod_cargo ) as cargo_criacao
  on contrato_servidor.cod_cargo = cargo_criacao.cod_cargo
join (select cod_sub_divisao, cod_cargo, max(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
      where timestamp <  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_sub_divisao, cod_cargo ) as cargo_sub_divisao
  on cargo_sub_divisao.cod_cargo = contrato_servidor.cod_cargo
 and cargo_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
join normas.norma
  on norma.cod_norma = contrato_servidor.cod_norma
join (select contrato_servidor_padrao.cod_padrao, contrato_servidor_padrao.cod_contrato
        from pessoal.contrato_servidor_padrao
            ,(select cod_contrato
                    , max(timestamp) as timestamp
              from pessoal.contrato_servidor_padrao
              where timestamp <  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
              group by cod_contrato) as max_contrato_servidor_padrao
              where contrato_servidor_padrao.cod_contrato  = max_contrato_servidor_padrao.cod_contrato
              and contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao
  on contrato_servidor_padrao.cod_contrato = contrato.cod_contrato
join (select padrao_padrao.cod_padrao, padrao_padrao.valor
      from folhapagamento.padrao_padrao
          ,(select cod_padrao, max(timestamp) as timestamp
              from folhapagamento.padrao_padrao
              where timestamp <  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_padrao) as ultimo_padrao
      where padrao_padrao.cod_padrao = ultimo_padrao.cod_padrao
        and padrao_padrao.timestamp = ultimo_padrao.timestamp) as padrao_padrao
  on padrao_padrao.cod_padrao = contrato_servidor_padrao.cod_padrao
join (select contrato_servidor_orgao.cod_orgao, contrato_servidor_orgao.cod_contrato
      from pessoal.contrato_servidor_orgao
          ,(select cod_orgao, max(timestamp) as timestamp
              from pessoal.contrato_servidor_orgao
              where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_orgao) as ultimo_contrato_servidor_orgao
      where contrato_servidor_orgao.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao
        and contrato_servidor_orgao.timestamp = ultimo_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
  on contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
join organograma.orgao
  on orgao.cod_orgao = contrato_servidor_orgao.cod_orgao
join pessoal.servidor_contrato_servidor
  on servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
join (select servidor_ctps.cod_servidor, ctps.numero, ctps.serie
      from pessoal.servidor_ctps
          ,(select servidor_ctps.cod_servidor
                  ,max(servidor_ctps.cod_ctps) as cod_ctps
              from pessoal.servidor_ctps
              join pessoal.ctps
                on ctps.cod_ctps = servidor_ctps.cod_ctps
              where ctps.dt_emissao < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_servidor) as ultimo_ctps
          ,pessoal.ctps
      where servidor_ctps.cod_servidor = ultimo_ctps.cod_servidor
        and servidor_ctps.cod_ctps = ultimo_ctps.cod_ctps
        and ctps.cod_ctps = servidor_ctps.cod_ctps) as servidor_ctps
  on servidor_ctps.cod_servidor = servidor_contrato_servidor.cod_servidor
where cargo_sub_divisao.cod_sub_divisao in (3, 5)
  and norma.dt_assinatura between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
";

    return $stSql;
}

function recuperaTodosComTipoContrato(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.registro ";
    $stSql = $this->montaRecuperaTodosComTipoContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodosComTipoContrato()
{
    $stSql  = "   SELECT contrato.*                                                             \n";
    $stSql .= "        , CASE WHEN contrato_servidor.cod_contrato IS NOT NULL THEN 'Servidor'   \n";
    $stSql .= "               ELSE 'Pensionista' END AS tipo                                    \n";
    $stSql .= "     FROM pessoal.contrato                                                       \n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor                                              \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato.cod_contrato                 \n";

    return $stSql;
}

function recuperaContratosFerias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratos()
{
    $stSql  = "SELECT contrato.*                                                                                        \n";
    $stSql .= "     , servidor.numcgm                                                                                   \n";
    $stSql .= "  FROM pessoal.contrato                                                                                  \n";
    $stSql .= "     , pessoal.contrato_servidor_regime_funcao                                                           \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                            \n";
    $stSql .= "               , max(timestamp) as timestamp                                                             \n";
    $stSql .= "            FROM pessoal.contrato_servidor_regime_funcao                                                 \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                    \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                \n";
    $stSql .= "     , pessoal.servidor                                                                                  \n";
    $stSql .= " WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato   \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.timestamp    = max_contrato_servidor_regime_funcao.timestamp      \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.cod_contrato = contrato.cod_contrato                              \n";
    $stSql .= "   AND contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                   \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                   \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                                              \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa                                           \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato   )          \n";

    return $stSql;
}

function recuperaContratosCalculoFolha(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosCalculoFolha",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosCalculoFolha()
{
    $boAtivos       = $this->getDado("boAtivos");
    $boAposentados  = $this->getDado("boAposentados");
    $boRescindidos  = $this->getDado("boRescindidos");
    $boPensionistas = $this->getDado("boPensionistas");
    $inCodLocal     = $this->getDado("inCodLocal");
    $inCodLotacao   = $this->getDado("inCodLotacao");
    $inCodEvento    = $this->getDado("inCodEvento");
    $stTipoFolha    = $this->getDado("stTipoFolha");
    $inCodComplementar        = $this->getDado("inCodComplementar");
    $inCodPeriodoMovimentacao = $this->getDado("inCodPeriodoMovimentacao");
    $dtFinalCompetencia       = $this->getDado("dtFinalCompetencia");
    $stFiltro                 = "";

    $arSituacaoContrato = array();
    if ($boAtivos === true) {
        array_push($arSituacaoContrato,"'A'");
    }
    if ($boAposentados === true) {
        array_push($arSituacaoContrato,"'P'");
    }
    if ($boRescindidos === true) {
        array_push($arSituacaoContrato,"'R'");
    }

    $stSql  = "SELECT * FROM ( \n";

    if ($boAtivos === true || $boAposentados === true || $boRescindidos === true || $boPensionistas === true) {
        // Monta a consulta dos servidores(Ativos, aposentado e rescindidos)
        $stSql .= "SELECT contrato.*                                                        \n";
        $stSql .= "     , sw_cgm.numcgm                                                     \n";
        $stSql .= "     , sw_cgm.nom_cgm                                                    \n";
        $stSql .= "  FROM pessoal.contrato                                                  \n";
        $stSql .= "  JOIN pessoal.servidor_contrato_servidor                                \n";
        $stSql .= "    ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato   \n";
        $stSql .= "  JOIN pessoal.servidor                                                  \n";
        $stSql .= "    ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor   \n";
        $stSql .= "  JOIN sw_cgm                                                            \n";
        $stSql .= "    ON servidor.numcgm = sw_cgm.numcgm                                   \n";
        $stSql .= "  JOIN pessoal.contrato_servidor                                         \n";
        $stSql .= "    ON contrato_servidor.cod_contrato = contrato.cod_contrato            \n";

        // Adicionando Filtro por Local para os servidores
        if (trim($inCodLocal) != "") {
            $stSql .= "  JOIN pessoal.contrato_servidor_local                                                   \n";
            $stSql .= "    ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                      \n";
            $stSql .= "  JOIN (  SELECT cod_contrato                                                            \n";
            $stSql .= "               , MAX(timestamp) as timestamp                                             \n";
            $stSql .= "            FROM pessoal.contrato_servidor_local                                         \n";
            $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_local                            \n";
            $stSql .= "    ON contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato   \n";
            $stSql .= "   AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp         \n";
            $stSql .= "   AND contrato_servidor_local.cod_local IN (".$inCodLocal.")                            \n";
        }

        // Adicionando Filtro por Lotação para os servidores
        if (trim($inCodLotacao) != "") {
            $stSql .= "  JOIN pessoal.contrato_servidor_orgao                                                   \n";
            $stSql .= "    ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato                      \n";
            $stSql .= "  JOIN (  SELECT cod_contrato                                                            \n";
            $stSql .= "               , MAX(timestamp) as timestamp                                             \n";
            $stSql .= "            FROM pessoal.contrato_servidor_orgao                                         \n";
            $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_orgao                            \n";
            $stSql .= "    ON contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato   \n";
            $stSql .= "   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp         \n";
            $stSql .= "   AND contrato_servidor_orgao.cod_orgao IN (".$inCodLotacao.")                          \n";
        }

        // Adicionando Filtro por Evento para os servidores
        if (trim($inCodEvento) != "") {
            $stSql .= "  INNER JOIN folhapagamento.registro_evento_periodo                                \n";
            $stSql .= "          ON registro_evento_periodo.cod_contrato = contrato.cod_contrato          \n";
            $stSql .= "  INNER JOIN folhapagamento.registro_evento                                        \n";
            $stSql .= "          ON registro_evento.cod_registro = registro_evento_periodo.cod_registro   \n";
            $stSql .= "         AND registro_evento.cod_evento IN ( ".$inCodEvento." )                    \n";
        }

        if (count($arSituacaoContrato) > 0) {
            $stSql .= " WHERE recuperarSituacaoDoContrato(contrato.cod_contrato, 0, '".Sessao::getEntidade()."') in (".implode(",",$arSituacaoContrato).") \n";
        }

        if ($boPensionistas === true) {
            $stSql .= "UNION \n";
        }
    }

    if ($boPensionistas === true) {
        // Monta a consulta dos pensionistas
        $stSql .= "SELECT contrato.*                                                                    \n";
        $stSql .= "     , pensionista.numcgm                                                            \n";
        $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = pensionista.numcgm) as nom_cgm     \n";
        $stSql .= "  FROM pessoal.contrato                                                              \n";
        $stSql .= "  JOIN pessoal.contrato_pensionista                                                  \n";
        $stSql .= "    ON (contrato.cod_contrato = contrato_pensionista.cod_contrato)                   \n";
        $stSql .= "  JOIN pessoal.pensionista                                                           \n";
        $stSql .= "    ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista            \n";
        $stSql .= "   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente  \n";

        if (trim($inCodLotacao) != "") {
            // Adicionando Filtro por Lotação para os pensionistas
            $stSql .= "   JOIN pessoal.contrato_pensionista_orgao                                                     \n";
            $stSql .= "     ON contrato.cod_contrato = contrato_pensionista.cod_contrato                              \n";
            $stSql .= "   JOIN (  SELECT cod_contrato                                                                 \n";
            $stSql .= "                , max(timestamp) as timestamp                                                  \n";
            $stSql .= "             FROM pessoal.contrato_pensionista_orgao                                           \n";
            $stSql .= "         GROUP BY cod_contrato) as max_contrato_pensionista_orgao                              \n";
            $stSql .= "     ON contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato  \n";
            $stSql .= "    AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp        \n";
            $stSql .= "    AND contrato_pensionista_orgao.cod_orgao IN (".$inCodLotacao.")                            \n";
        }

        // Adicionando Filtro por Evento para os servidores
        if (trim($inCodEvento) != "") {
            $stSql .= "  INNER JOIN folhapagamento.registro_evento_periodo                                   \n";
            $stSql .= "          ON registro_evento_periodo.cod_contrato = contrato.cod_contrato             \n";
            $stSql .= "  INNER JOIN folhapagamento.registro_evento                                           \n";
            $stSql .= "          ON registro_evento.cod_registro = registro_evento_periodo.cod_registro      \n";
            $stSql .= "         AND registro_evento.cod_evento IN ( ".$inCodEvento." )                       \n";
        }
    }
    $stSql .= " ) as contrato \n";

    // Verifica se o contrato possui registros de eventos para ser calculado na folha na competencia
    switch (trim($stTipoFolha)) {
        case "S":
            $stFiltro .= " WHERE EXISTS ( SELECT 1                                                                                  \n";
            $stFiltro .= "                  FROM folhapagamento.registro_evento_periodo                                             \n";
            $stFiltro .= "                  JOIN folhapagamento.ultimo_registro_evento                                              \n";
            $stFiltro .= "                    ON ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro         \n";
            $stFiltro .= "                 WHERE registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."   \n";
            $stFiltro .= "                   AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                       \n";
            $stFiltro .= "               )                                                                                          \n";
            break;
        case "C":
            $stFiltro .= " WHERE EXISTS ( SELECT 1                                                                                                      \n";
            $stFiltro .= "                  FROM folhapagamento.registro_evento_complementar                                                            \n";
            $stFiltro .= "                  JOIN folhapagamento.ultimo_registro_evento_complementar                                                     \n";
            $stFiltro .= "                    ON ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro           \n";
            $stFiltro .= "                   AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar.cod_evento               \n";
            $stFiltro .= "                   AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao   \n";
            $stFiltro .= "                   AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp                 \n";
            $stFiltro .= "                 WHERE registro_evento_complementar.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."                  \n";
            $stFiltro .= "                   AND registro_evento_complementar.cod_complementar = ".$inCodComplementar."                                 \n";
            $stFiltro .= "                   AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                      \n";
            $stFiltro .= "              )                                                                                                               \n";
            break;
        case "F":
            $stFiltro .= " WHERE EXISTS ( SELECT 1                                                                                      \n";
            $stFiltro .= "                  FROM pessoal.ferias                                                                         \n";
            $stFiltro .= "                     , pessoal.lancamento_ferias                                                              \n";
            $stFiltro .= "                     , folhapagamento.registro_evento_ferias                                                  \n";
            $stFiltro .= "                  JOIN folhapagamento.ultimo_registro_evento_ferias                                           \n";
            $stFiltro .= "                    ON ultimo_registro_evento_ferias.cod_registro = registro_evento_ferias.cod_registro       \n";
            $stFiltro .= "                   AND ultimo_registro_evento_ferias.cod_evento = registro_evento_ferias.cod_evento           \n";
            $stFiltro .= "                   AND ultimo_registro_evento_ferias.desdobramento = registro_evento_ferias.desdobramento     \n";
            $stFiltro .= "                   AND ultimo_registro_evento_ferias.timestamp = registro_evento_ferias.timestamp             \n";
            $stFiltro .= "                 WHERE registro_evento_ferias.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."        \n";
            $stFiltro .= "                   AND ferias.cod_ferias = lancamento_ferias.cod_ferias                                       \n";
            $stFiltro .= "                   AND ferias.cod_contrato = contrato.cod_contrato                                            \n";
            $stFiltro .= "                   AND lancamento_ferias.cod_tipo = 1                                                         \n";
            $stFiltro .= "                   AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                            \n";
            $stFiltro .= "             )                                                                                                \n";
            break;
        case "D":
            $stFiltro .= " WHERE EXISTS ( SELECT 1                                                                                           \n";
            $stFiltro .= "                  FROM folhapagamento.concessao_decimo                                                             \n";
            $stFiltro .= "                     , folhapagamento.registro_evento_decimo                                                       \n";
            $stFiltro .= "                  JOIN folhapagamento.ultimo_registro_evento_decimo                                                \n";
            $stFiltro .= "                    ON ultimo_registro_evento_decimo.cod_registro = registro_evento_decimo.cod_registro            \n";
            $stFiltro .= "                   AND ultimo_registro_evento_decimo.cod_evento = registro_evento_decimo.cod_evento                \n";
            $stFiltro .= "                   AND ultimo_registro_evento_decimo.desdobramento = registro_evento_decimo.desdobramento          \n";
            $stFiltro .= "                   AND ultimo_registro_evento_decimo.timestamp = registro_evento_decimo.timestamp                  \n";
            $stFiltro .= "                 WHERE registro_evento_decimo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."             \n";
            $stFiltro .= "                   AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                 \n";
            $stFiltro .= "                   AND registro_evento_decimo.cod_contrato = concessao_decimo.cod_contrato                         \n";
            $stFiltro .= "                   AND registro_evento_decimo.cod_periodo_movimentacao = concessao_decimo.cod_periodo_movimentacao \n";
            $stFiltro .= "                   AND concessao_decimo.folha_salario = 'f'                                                        \n";
            $stFiltro .= "              )                                                                                                    \n";
            break;
        case "R":
            $stFiltro .= " WHERE EXISTS ( SELECT 1                                                                                          \n";
            $stFiltro .= "                  FROM folhapagamento.registro_evento_rescisao                                                    \n";
            $stFiltro .= "                  JOIN folhapagamento.ultimo_registro_evento_rescisao                                             \n";
            $stFiltro .= "                    ON ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro       \n";
            $stFiltro .= "                   AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento           \n";
            $stFiltro .= "                   AND ultimo_registro_evento_rescisao.desdobramento = registro_evento_rescisao.desdobramento     \n";
            $stFiltro .= "                   AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp             \n";
            $stFiltro .= "                 WHERE registro_evento_rescisao.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."          \n";
            $stFiltro .= "                   AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                              \n";
            $stFiltro .= "               )                                                                                                  \n";
            break;
    }
    $stSql .= $stFiltro;

    return $stSql;
}

function recuperaPorCPF(&$rsRecordSet, $cpf, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPorCPF($cpf);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPorCPF($cpf)
{
    $stSql = " select
                    servidor_contrato_servidor.cod_contrato,
                    cgm.numcgm,
                    posse.timestamp
                from
                    pessoal.servidor
                join
                    sw_cgm_pessoa_fisica as cgm
                on
                    servidor.numcgm = cgm.numcgm
                join
                    pessoal.servidor_contrato_servidor
                on
                    servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                join
                    (
                        select
                             cod_contrato
                            ,max(timestamp) as timestamp
                        from
                            pessoal.contrato_servidor_nomeacao_posse
                        group by
                            cod_contrato
                    ) as posse
                on
                    posse.cod_contrato = servidor_contrato_servidor.cod_contrato
                where
                    not exists (
                                select
                                    1
                                from
                                    pessoal.contrato_servidor_caso_causa
                                where
                                    contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato
                               )
                    and cgm.cpf = '$cpf'
                order by
                    timestamp asc limit 1";

    return $stSql;
}

/**
 * Função exclusiva para uso no MANAD (Manual Normativo de Arquivos Digitais)
 *
 * Bloco K - Folha de Pagamento
 *
 * @access Public
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stCondicao  Array de condição do SQL (WHERE)
 * @param  String  $stOrdem     Array de Ordenação do SQL (ORDER BY)
 * @param  Boolean $boTransacao
 * @return Array   Array        Array de RecordSet, sendo o tipo de registro a chave.
 */
function recuperaDadosMANAD($dtInicial, $dtFinal, $stEntidades, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $arRegistros = array();

    # Entidades (menos a prefeitura)
    $stSql =  $this->montaRecuperaEntidadesRH(Sessao::getExercicio());
    $obErro = $obConexao->executaSQL($rsEntidades, $stSql, $boTransacao);
    if ($obErro->ocorreu()) {
        return $obErro;
    }

    # Descobre os códigos do período de movimentação para determinada data.
    $stWhere = " AND FPM.dt_inicial BETWEEN '".$dtInicial."' AND '".$dtFinal."' AND FPM.dt_final BETWEEN '".$dtInicial."' AND '".$dtFinal."' ";
    $obPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodosMovimentacao, $stWhere);
    foreach ($rsEntidades->getElementos() as $row) {
        $obPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodosMovimentacao, $stWhere, "", "", "folhapagamento_".$row['cod_entidade']);
    }

    $stWhere = " AND entidade.exercicio = '".$this->getDado('stExercicio')."' ";
    $stSql = $this->montaRecuperaDadosRegistroK050MANAD().$stWhere;

    // Registro tipo K050.
    foreach ($rsEntidades->getElementos() as $row) {
        $stSql .= " UNION ";
        $stSql .= $this->montaRecuperaDadosRegistroK050MANAD(false, "pessoal_".$row['cod_entidade'], 0, $row['cod_entidade']).$stWhere;
    }

    $obErro = $obConexao->executaSQL($rsRecordSetAux, $stSql, $boTransacao);
    $arRegistros["K050"] = $rsRecordSetAux;

    //Resgistro tipo K100.

}

/**
 * Recupera as Entidades do RH, excluindo a prefeitura
 */
function montaRecuperaEntidadesRH()
{
    $stSql  = " SELECT DISTINCT entidade_rh.cod_entidade                                                                                              \n";
    $stSql .= "   FROM administracao.entidade_rh                                                                                                      \n";
    $stSql .= "  WHERE entidade_rh.exercicio = '".$this->getDado('stExercicio')."'                                                                    \n";
    $stSql .= "    AND entidade_rh.cod_entidade <> (SELECT configuracao.valor                                                                         \n";
    $stSql .= "                                       FROM administracao.configuracao                                                                 \n";
    $stSql .= "                                      WHERE configuracao.exercicio = '".$this->getDado('stExercicio')."'                               \n";
    $stSql .= "                                        AND parametro = 'cod_entidade_prefeitura' )                                                    \n";

    return $stSql;
}

/**
 * Query para uso no MANAD (Manual Normativo de Arquivos Digitais)
 *
 * Bloco K - Folha de Pagamento
 *
 * @param  string  $schemaPessoal string Schema Pessoal
 */
function montaRecuperaDadosRegistroK050MANAD($usarEntidadePadrao=true, $schemaPessoal="pessoal", $codPeriodoMovimentacao=0, $stEntidade='')
{
    $stSql  = "  SELECT 'K050' as reg                                                                                                                                                                             \n";
    $stSql .= "         , sw_cgm_pessoa_juridica.cnpj                                                                                                                                                             \n";
    $stSql .= "         , servidores.*                                                                                                                                                                            \n";
    $stSql .= "    FROM ( SELECT to_char((select pega0datafinalcompetenciadoperiodomovimento(".$codPeriodoMovimentacao.")::date), 'dd/mm/yyyy') as dt_inc_alt                                                     \n";
    $stSql .= "                , contrato.registro as cod_reg_trab                                                                                                                                                \n";
    $stSql .= "                , servidor_contrato.cpf                                                                                                                                                            \n";
    $stSql .= "                , servidor_contrato.nit                                                                                                                                                            \n";
    $stSql .= "                , servidor_contrato.cod_categ                                                                                                                                                      \n";
    $stSql .= "                , servidor_contrato.nome_trab                                                                                                                                                      \n";
    $stSql .= "                , servidor_contrato.dt_nasc                                                                                                                                                        \n";
    $stSql .= "                , servidor_contrato.dt_admissao                                                                                                                                                    \n";
    $stSql .= "                , servidor_contrato.dt_demissao                                                                                                                                                    \n";
    $stSql .= "                , servidor_contrato.ind_vinc                                                                                                                                                       \n";
    $stSql .= "                , servidor_contrato.tipo_ato_nom                                                                                                                                                   \n";
    $stSql .= "                , servidor_contrato.nm_ato_nom                                                                                                                                                     \n";
    $stSql .= "                , servidor_contrato.dt_ato_nom                                                                                                                                                     \n";
    $stSql .= "             FROM ".$schemaPessoal.".contrato                                                                                                                                                      \n";
    $stSql .= "                  , ( SELECT sw_cgm_pessoa_fisica.cpf as cpf                                                                                                                                       \n";
    $stSql .= "                           , sw_cgm_pessoa_fisica.servidor_pis_pasep as nit                                                                                                                        \n";
    $stSql .= "                           , cod_categoria as cod_categ                                                                                                                                            \n";
    $stSql .= "                           , sw_cgm.nom_cgm as nome_trab                                                                                                                                           \n";
    $stSql .= "                           , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as dt_nasc                                                                                                   \n";
    $stSql .= "                           , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao::date,'dd/mm/yyyy') as dt_admissao                                                                        \n";
    $stSql .= "                           , to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao,'dd/mm/yyyy') as dt_demissao                                                                                  \n";
    $stSql .= "                           , normas.cod_tipo_norma as tipo_ato_nom                                                                                                                                 \n";
    $stSql .= "                           , normas.numero_norma as nm_ato_nom                                                                                                                                     \n";
    $stSql .= "                           , normas.dt_publicacao as dt_ato_nom                                                                                                                                    \n";
    $stSql .= "                           , ( CASE WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (4,7) THEN 3                                                                        \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (1,2) THEN 4                                                                        \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (3,99,5) THEN 9                                                                     \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (6) THEN 5                                                                          \n";
    $stSql .= "                                    WHEN ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN (8,100) THEN 7                                                                      \n";
    $stSql .= "                                    ELSE 9                                                                                                                                                         \n";
    $stSql .= "                              END ) as ind_vinc                                                                                                                                                    \n";
    $stSql .= "                           , ( SELECT descricao                                                                                                                                                    \n";
    $stSql .= "                                 FROM ".$schemaPessoal.".cargo                                                                                                                                     \n";
    $stSql .= "                                WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo                                                                                                        \n";
    $stSql .= "                             ) as desc_cargo                                                                                                                                                       \n";
    $stSql .= "                           , ( SELECT orgao                                                                                                                                                        \n";
    $stSql .= "                                 FROM organograma.vw_orgao_nivel                                                                                                                                   \n";
    $stSql .= "                                WHERE cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao                                                                                                         \n";
    $stSql .= "                             ) as cod_ltc                                                                                                                                                          \n";
    $stSql .= "                           , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao                                                                                                       \n";
    $stSql .= "                           , to_date((select pega0datafinalcompetenciadoperiodomovimento(509)), 'yyyy-mm-dd')) as desc_ltc                                                                         \n";
    $stSql .= "                           , cbo.codigo as cod_cbo                                                                                                                                                 \n";
    $stSql .= "                            FROM ".$schemaPessoal.".contrato_servidor                                                                                                                              \n";
    $stSql .= "                      INNER JOIN ".$schemaPessoal.".servidor_contrato_servidor                                                                                                                     \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                                          \n";
    $stSql .= "                      INNER JOIN ".$schemaPessoal.".servidor                                                                                                                                       \n";
    $stSql .= "                              ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                                   \n";
    $stSql .= "                      INNER JOIN sw_cgm                                                                                                                                                            \n";
    $stSql .= "                              ON servidor.numcgm = sw_cgm.numcgm                                                                                                                                   \n";
    $stSql .= "                      INNER JOIN sw_cgm_pessoa_fisica                                                                                                                                              \n";
    $stSql .= "                              ON sw_cgm_pessoa_fisica.numcgm=sw_cgm.numcgm                                                                                                                         \n";
    $stSql .= "                      INNER JOIN ultimo_contrato_servidor_orgao('".$stEntidade."', '".$codPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao                                                \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato                                                                                      \n";
    $stSql .= "                      INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$stEntidade."', '".$codPeriodoMovimentacao."') as ultimo_contrato_servidor_nomeacao_posse                              \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato                                                                             \n";
    $stSql .= "                      INNER JOIN ultimo_contrato_servidor_funcao('".$stEntidade."', '".$codPeriodoMovimentacao."') as ultimo_contrato_servidor_funcao                                              \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato                                                                                     \n";
    $stSql .= "                      INNER JOIN ultimo_contrato_servidor_regime_funcao('".$stEntidade."', '".$codPeriodoMovimentacao."') as ultimo_contrato_servidor_regime_funcao                                \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato                                                                              \n";
    $stSql .= "                      INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$stEntidade."', '".$codPeriodoMovimentacao."') as ultimo_contrato_servidor_sub_divisao_funcao                      \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato                                                                         \n";
    $stSql .= "                      INNER JOIN (    SELECT cbo_cargo.cod_cargo                                                                                                                                   \n";
    $stSql .= "                                           , cbo_cargo.cod_cbo                                                                                                                                     \n";
    $stSql .= "                                           , cbo.codigo                                                                                                                                            \n";
    $stSql .= "                                        FROM ".$schemaPessoal.".cbo                                                                                                                                \n";
    $stSql .= "                                           , ".$schemaPessoal.".cbo_cargo                                                                                                                          \n";
    $stSql .= "                                  INNER JOIN (    SELECT cbo_cargo.cod_cargo                                                                                                                       \n";
    $stSql .= "                                                       , max(cbo_cargo.timestamp) as timestamp                                                                                                     \n";
    $stSql .= "                                                    FROM ".$schemaPessoal.".cbo_cargo                                                                                                              \n";
    $stSql .= "                                                   WHERE cbo_cargo.timestamp <= (select ultimotimestampperiodomovimentacao(".$codPeriodoMovimentacao.",'".$stEntidade."'))                         \n";
    $stSql .= "                                                GROUP BY cbo_cargo.cod_cargo                                                                                                                       \n";
    $stSql .= "                                              ) as max_cbo_cargo                                                                                                                                   \n";
    $stSql .= "                                          ON max_cbo_cargo.cod_cargo = cbo_cargo.cod_cargo                                                                                                         \n";
    $stSql .= "                                         AND max_cbo_cargo.timestamp = cbo_cargo.timestamp                                                                                                         \n";
    $stSql .= "                                       WHERE cbo.cod_cbo=cbo_cargo.cod_cbo                                                                                                                         \n";
    $stSql .= "                                  ) as cbo                                                                                                                                                         \n";
    $stSql .= "                              ON cbo.cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo                                                                                                         \n";
    $stSql .= "                       LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$stEntidade."', '".$codPeriodoMovimentacao."') as ultimo_contrato_servidor_especialidade_funcao                  \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_especialidade_funcao.cod_contrato                                                                       \n";
    $stSql .= "                       LEFT JOIN ultimo_contrato_servidor_caso_causa('".$stEntidade."', '".$codPeriodoMovimentacao."') as ultimo_contrato_servidor_caso_causa                                      \n";
    $stSql .= "                              ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_caso_causa.cod_contrato                                                                                 \n";
    $stSql .= "                             AND ultimo_contrato_servidor_caso_causa.dt_rescisao <= to_date((select pega0datafinalcompetenciadoperiodomovimento(".$codPeriodoMovimentacao.")::DATE), 'yyyy-mm-dd') \n";
    $stSql .= "                       LEFT JOIN ( SELECT  cod_norma                                                                                                                                               \n";
    $stSql .= "                                         , cod_tipo_norma                                                                                                                                          \n";
    $stSql .= "                                         , norma.num_norma||'/'||norma.exercicio as numero_norma                                                                                                   \n";
    $stSql .= "                                         , to_char(norma.dt_publicacao,'dd/mm/yyyy') as dt_publicacao                                                                                              \n";
    $stSql .= "                                    FROM normas.norma                                                                                                                                              \n";
    $stSql .= "                                 ) as normas                                                                                                                                                       \n";
    $stSql .= "                              ON normas.cod_norma = contrato_servidor.cod_norma                                                                                                                    \n";
    $stSql .= "                  ) as servidor_contrato                                                                                                                                                           \n";
    $stSql .= "            WHERE contrato.cod_contrato = servidor_contrato.cod_contrato                                                                                                                           \n";
    $stSql .= "          ) as servidores                                                                                                                                                                          \n";
    $stSql .= "          , orcamento.entidade                                                                                                                                                                     \n";
    $stSql .= " INNER JOIN sw_cgm ON sw_cgm.numcgm = entidade.numcgm                                                                                                                                              \n";
    $stSql .= " INNER JOIN sw_cgm_pessoa_juridica ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm                                                                                                                \n";
    $stSql .= "      WHERE to_date(servidores.dt_admissao,'dd-mm-yyyy') < (select pega0datafinalcompetenciadoperiodomovimento(509)::date)                                                                         \n";

    if ($usarEntidadePadrao) {
        $stSql .= "    AND entidade.cod_entidade = ( SELECT configuracao.valor                                                                                                                                    \n";
        $stSql .= "                                    FROM administracao.configuracao                                                                                                                            \n";
        $stSql .= "                                   WHERE configuracao.exercicio = '".$this->getDado('stExercicio')."'                                                                                          \n";
        $stSql .= "                                     AND configuracao.parametro = 'cod_entidade_prefeitura' )                                                                                                  \n";
    }

    return $stSql;
}

}
