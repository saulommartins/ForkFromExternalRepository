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
  * Classe de mapeamento da tabela PESSOAL.CARGO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
  * Casos de uso: uc-03.01.02, uc-03.01.19

*/

/*
$Log$
Revision 1.25  2007/10/11 15:42:51  bruce
 Ticket#10272#

Revision 1.24  2007/10/05 13:00:16  hboaventura
inclusão dos arquivos

Revision 1.23  2007/07/05 13:38:46  hboaventura
Bug#9568#

Revision 1.22  2007/06/19 20:52:49  hboaventura
Bug#9422#

Revision 1.21  2007/06/18 21:39:18  hboaventura
Inclusão do campo nota fiscal

Revision 1.20  2007/06/18 19:59:47  hboaventura
Inclusão do campo nota fiscal

Revision 1.19  2007/06/15 21:23:39  hboaventura
bug #9411#

Revision 1.18  2007/06/11 19:25:46  rodrigo
Correção nos atributos dinamicos na query de consulta.

Revision 1.17  2007/05/21 19:24:08  rodrigo_sr
Bug #8847#

Revision 1.16  2007/05/17 20:21:04  rodrigo_sr
Bug #8847#

Revision 1.15  2007/05/17 19:07:59  hboaventura
Alteração no Relatório Customizável

Revision 1.14  2007/03/23 21:33:54  tonismar
ajuste no relatório customizável

Revision 1.13  2007/03/22 21:00:25  tonismar
solicitação da prefeitura incluído filtro por entidade

Revision 1.12  2007/02/09 15:22:00  tonismar
bug #6946

Revision 1.11  2007/02/08 19:06:55  tonismar
bug #6946

Revision 1.10  2007/02/08 16:44:42  tonismar
bug #6946

Revision 1.9  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CARGO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPatrimonioAtributoPatrimonio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioAtributoPatrimonio()
{
    parent::Persistente();
    $this->setTabela('administracao.atributo_dinamico');

    $this->setCampoCod('cod_atributo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_atributo','integer',true,'',true,false);
    $this->AddCampo('nom_atributo','varchar',true,'60',false,false);
    $this->AddCampo('cod_tipo','varchar',true,'1',false,false);
    $this->AddCampo('valor_padrao','text',true,'',false,false);
}

function recuperaNomeAtributo(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
   $obErro      = new Erro;
   $obConexao   = new Conexao;
   $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNomeAtributo().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNomeAtributo()
{
    $stSql="
        SELECT atributo_dinamico.nom_atributo
             , atributo_dinamico.cod_atributo
          FROM administracao.atributo_dinamico
         WHERE atributo_dinamico.cod_cadastro = 1
           AND atributo_dinamico.cod_modulo = 6
      ORDER BY nom_atributo
            ";

    return $stSql;
}

function RecuperaRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelatorio($stFiltro);
    $this->setDebug($stSql);    
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorio($filtro)
{
        $in  ="(";

        for ($icount=0;$icount <=$filtro['cont'];$icount++) {
            if ($Campo=='' && $in=='(') {
               if ($filtro['boAtributoDinamico'.$icount.'']) {
                  $inCodAtributo = $filtro['boAtributoDinamico'.$icount];
                  $max .="  max(dinamico.Atributo_".$inCodAtributo.")  as valor_atributo".$inCodAtributo.",";

                  $Campo = "case when bae.cod_atributo =".$inCodAtributo."
                            then bae.valor
                            else null
                            end as Atributo_".$inCodAtributo."";

                     $in .="".$inCodAtributo.",";

                }
            }else
             if ($filtro['boAtributoDinamico'.$icount.'']) {
                  $inCodAtributo = $filtro['boAtributoDinamico'.$icount];
                  $max .="  max(dinamico.Atributo_".$inCodAtributo.")  as valor_atributo".$inCodAtributo.",";

                  $Campo .= ", case when bae.cod_atributo =".$inCodAtributo."
                               then bae.valor
                               else null
                               end as Atributo_".$inCodAtributo."";

                     $in .="".$inCodAtributo.",";
               }

            if ($filtro['ordenacao']==$icount AND !strstr($filtro['ordenacao'],'bo') ) {
               $ordenacao=", valor_atributo".$filtro['ordenacao']."";
            }
            if ($filtro['filtro']==$icount AND !strstr($filtro['ordenacao'],'bo')) {
               $ordenacao=", valor_atributo".$filtro['ordenacao']."";
            }

        }
        $in = substr($in,0,-1);
        $in .=")";

        if ($filtro['codNatureza'] && $filtro['codNatureza']!='xxx') {
           $Compara =" and ";
           $Compara .="bae.cod_natureza =".$filtro['codNatureza'] ."";
        }
        if ($filtro['codGrupo'] && $filtro['codGrupo']!='xxx') {
           $Compara .="  and ";
           $Compara .="bae.cod_grupo =".$filtro['codGrupo'] ."";
        }
        if ($filtro['codEspecie'] && $filtro['codEspecie']!='xxx') {
           $Compara .=" and  ";
           $Compara .="bae.cod_especie =".$filtro['codEspecie'] ."";
        }

        if ($filtro['boOrdenaData']) {
            $ordenacao .=", dt_aquisicao";
        }
        if ($filtro['boOrdenaCodigo']) {
            $ordenacao .=", cod_bem";
        }
        if ($filtro['ordenacao'] == 'boValor') {
            $ordenacao.= ", vl_bem";
        }
        if ($filtro['ordenacao'] == 'boEmpenho') {
            $ordenacao.= ", bc.cod_empenho";
        }
        if ($filtro['ordenacao'] == 'boPlaca') {
            $ordenacao.= ", num_placa";
        }
        if ($filtro['ordenacao'] == 'boAquisicao') {
            $ordenacao.= ", dt_aquisicao";
        }
        if ($filtro['ordenacao'] == 'boDataBaixa') {
            $ordenacao.= ", MAX(dt_baixa) ";
        }
        if ($filtro['ordenacao'] == 'boNotaFiscal') {
            $ordenacao.= ", nota_fiscal";
        }

        if ( strlen($ordenacao) > 1 ) {
            $ordenacao = " order by ".substr($ordenacao,2,strlen($ordenacao));
        }

      if ($filtro['boEmpenho'] or $filtro['boValor']) {
          $stJoin = 'INNER';
      } else {
          $stJoin = 'LEFT';
      }

      if ( $filtro['stRBemBaixado'] == 'todos' ) {
          $stSelectBemBaixado = "max(to_char(bb.dt_baixa,'dd-mm-YYYY'))           as dt_baixa, ";
          $stJoinBemBaixado   = "LEFT OUTER JOIN patrimonio.bem_baixado bb
                                        on dinamico.cod_bem = bb.cod_bem ";
      }elseif ( $filtro['stRBemBaixado'] == 'sim' ) {
          $stSelectBemBaixado = "max(to_char(bb.dt_baixa,'dd-mm-YYYY'))           as dt_baixa, ";
          $stJoinBemBaixado   = "JOIN patrimonio.bem_baixado bb
                                       on dinamico.cod_bem = bb.cod_bem ";
      }elseif ( $filtro['stRBemBaixado'] == 'nao' ) {
          $stSelectBemBaixado  = " ''::varchar as dt_baixa, ";
          $stJoinBemBaixado    = " ";
          $boNotExistsBaixados = true ;          
      }

        $stSql.="   select  dinamico.cod_bem
                            ,dinamico.descricao
                            ,dinamico.vl_bem                                  as valor_empenho
                            ,max(bc.cod_empenho)                              as cod_empenho
                            ,max(dinamico.num_placa)                          as numero_placa
                            ,max(dinamico.classificacao)                      as classificacao
                            ,max(bc.nota_fiscal)                      		  as nota_fiscal
                            ,sw_cgm.nom_cgm 							      as entidade
                            ,max(to_char(dinamico.dt_aquisicao,'dd-mm-YYYY')) as dt_aquisicao,
                            ".$max."
                            ".$stSelectBemBaixado."
                            recuperaDescricaoOrgao(orgao.cod_orgao, to_date('".$filtro['dtFinal']."','dd/mm/yyyy')) as nom_orgao
                    from (  select bem.cod_bem
                                ,bem.descricao
                                ,bem.dt_aquisicao
                                ,bem.num_placa
                                ,bem.vl_bem
                                ,bem.cod_natureza|| '.' ||bem.cod_grupo|| '.' ||bem.cod_especie as classificacao
        ";
        if ( strlen($Campo) > 0 ) {
            $stSql.="                 ,".$Campo." \n ";
        }
        
        $stSql.="           from patrimonio.bem bem
                            left join patrimonio.bem_atributo_especie bae
                                on bae.cod_bem = bem.cod_bem
                            where
                            bem.dt_aquisicao between to_date('".$filtro['dtInicial']."','dd/mm/yyyy')
                                            and to_date('".$filtro['dtFinal']."','dd/mm/yyyy')
                            ".$Compara."
                            ) as dinamico
                            
                            ".$stJoinBemBaixado."

                            ".$stJoin." join patrimonio.bem_comprado bc
                                on bc.cod_bem = dinamico.cod_bem
                            inner join orcamento.entidade 
                                on entidade.cod_entidade = bc.cod_entidade
                                and entidade.exercicio    = bc.exercicio
                            inner join sw_cgm 
                                on entidade.numcgm = sw_cgm.numcgm \n";

        if ( ( $filtro['codEntidade'] >= 0 ) && ( $filtro['codEntidade'] != 'xxx' ) ) {
            $stSql.="       and sw_cgm.numcgm = ".$filtro['codEntidade']." ";
        }
        
        $stSql.="
                    left join (
                            SELECT  historico_bem.cod_bem
                                    , historico_bem.cod_orgao
                                    --, historico_bem.ano_exercicio
                                    , historico_bem.timestamp
                            FROM patrimonio.historico_bem
                            JOIN (  SELECT  cod_bem
                                         ,  MAX(timestamp) AS timestamp
                                    FROM  patrimonio.historico_bem
                                    GROUP BY  cod_bem
                                 ) AS historico_bem_max
                                ON historico_bem.cod_bem    = historico_bem_max.cod_bem
                                AND historico_bem.timestamp = historico_bem_max.timestamp
                            GROUP BY historico_bem.cod_orgao
                                    , historico_bem.cod_bem
                                    , historico_bem.timestamp
                            ) as historico_bem
                        ON historico_bem.cod_bem = dinamico.cod_bem      
                    LEFT JOIN organograma.orgao 
                        ON orgao.cod_orgao = historico_bem.cod_orgao
                    LEFT JOIN ( SELECT  em.exercicio
                                        , em.cod_empenho
                                        , ipe.vl_total
                                        , eai.vl_anulado 
                                FROM empenho.empenho em             
                                ".$stJoin." JOIN empenho.pre_empenho pe
                                    ON em.cod_pre_empenho = pe.cod_pre_empenho
                                ".$stJoin." JOIN empenho.item_pre_empenho ipe
                                    ON  pe.cod_pre_empenho = ipe.cod_pre_empenho
                                    AND pe.exercicio       = ipe.exercicio
                                LEFT OUTER JOIN empenho.empenho_anulado_item eai
                                    ON ipe.cod_pre_empenho = eai.cod_pre_empenho
                                    AND ipe.exercicio      = eai.exercicio
                                    AND ipe.num_item       = eai.num_item 
                            ) as emp
                        ON bc.cod_empenho = emp.cod_empenho
                        AND bc.exercicio = emp.exercicio 
        ";

      if ( strstr($filtro['filtro'],'bo') ) {
            if ($filtro['filtro'] == 'boValor') {
                $stFiltro = " vl_bem BETWEEN to_number('".floatval($filtro['valor_filtro1'])."','99999999999999.99') AND to_number('".floatval($filtro['valor_filtro2'])."','99999999999999.99') AND ";
            }
            if ($filtro['filtro'] == 'boEmpenho') {
                $stFiltro.= " bc.cod_empenho BETWEEN ".intval($filtro['valor_filtro1'])." AND ".intval($filtro['valor_filtro2'])." AND ";
            }
            if ($filtro['filtro'] == 'boPlaca') {
                $stFiltro.= " num_placa BETWEEN '".$filtro['valor_filtro1']."' AND '".$filtro['valor_filtro2']."' AND ";
            }
            if ($filtro['filtro'] == 'boAquisicao') {
                $stFiltro.= " dt_aquisicao BETWEEN to_date('".$filtro['valor_filtro1']."','dd/mm/yyyy') AND to_date('".$filtro['valor_filtro2']."','dd/mm/yyyy') AND ";
            }
            if ($filtro['filtro'] == 'boDataBaixa') {
                $stFiltro.= " dt_baixa BETWEEN to_date('".$filtro['valor_filtro1']."','dd/mm/yyyy') AND to_date('".$filtro['valor_filtro2']."','dd/mm/yyyy') AND ";
            }
            if ($filtro['filtro'] == 'boNotaFiscal') {
                $stFiltro.= " nota_fiscal BETWEEN '".$filtro['valor_filtro1']."' AND '".$filtro['valor_filtro2']."' AND ";
            }
      }
      if ($filtro[hdninCodOrganograma]<>'') {
          $arOrgao = explode("-",$filtro[codOrgao]);
          $stFiltro.= " historico_bem.cod_orgao in (select cod_orgao from organograma.vw_orgao_nivel where orgao_reduzido like '".$filtro[hdninCodOrganograma]."%') AND ";
      }

      if ($boNotExistsBaixados) {
          $stFiltro .= " NOT EXISTS (SELECT * FROM patrimonio.bem_baixado WHERE bem_baixado.cod_bem = dinamico.cod_bem) AND ";
      }

      if ($stFiltro) {
          $stSql.= " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4);
      }
      $stSql.="\n    group by orgao.cod_orgao,dinamico.cod_bem,dinamico.descricao,dinamico.vl_bem,dinamico.num_placa,bc.cod_empenho,nom_orgao,sw_cgm.nom_cgm ";
      if ( $filtro['filtro'] != 'xxx' AND !strstr($filtro['filtro'],'bo') ) {
          $stSql.="    HAVING max(upper(dinamico.Atributo_".$filtro['filtro'].")) between upper('".$filtro['valor_filtro1']."')         \n ";
          $stSql.="                                                             and upper('".$filtro['valor_filtro2']."')         \n ";
      }
      $stSql.= '  '.$ordenacao.' ';

      return $stSql;
}
}
