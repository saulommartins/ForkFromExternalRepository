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
    * Classe auxiliar de mapeamento
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCarneImobiliario.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.6  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.5  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

function recuperaListaReEmissaoImobiliario(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaListaReEmissao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaReEmissaoImobiliario()
{
    $stSql .= "        select ac.cod_calculo                                                                                                       \n";
    $stSql .= "             , al.cod_lancamento                                                                                                    \n";
    $stSql .= "             , ap.cod_parcela                                                                                                       \n";
    $stSql .= "             , mc.cod_credito                                                                                                       \n";
    $stSql .= "             , macg.cod_grupo                                                                                                       \n";
    $stSql .= "             , aece.inscricao_economica                                                                                             \n";
    $stSql .= "             , aece.cod_atividade                                                                                                   \n";
    $stSql .= "             , acn.cod_convenio                                                                                                     \n";
    $stSql .= "             , acn.exercicio                                                                                                        \n";
    $stSql .= "             , acn.cod_carteira                                                                                                     \n";
    $stSql .= "             , cgm.nom_cgm                                                                                                          \n";
    $stSql .= "          from arrecadacao.lancamento as al                                                                                         \n";
    $stSql .= "             , arrecadacao.parcela as ap                                                                                            \n";
    $stSql .= "             , arrecadacao.lancamento_calculo as alc                                                                                \n";
    $stSql .= "             , sw_cgm                         as cgm                                                                                \n";
    $stSql .= "             , (    SELECT                                                                                                          \n";
    $stSql .= "                        car.*                                                                                                       \n";
    $stSql .= "                     FROM                                                                                                           \n";
    $stSql .= "                         arrecadacao.carne car,                                                                                     \n";
    $stSql .= "                         (SELECT cod_parcela,max(timestamp) as timestamp FROM arrecadacao.carne GROUP BY cod_parcela)  maxcar  \n";
    $stSql .= "                     WHERE                                                                                                          \n";
    $stSql .= "                         car.cod_parcela = maxcar.cod_parcela and                                                                   \n";
    $stSql .= "                         car.timestamp = maxcar.timestamp                                                                           \n";
    $stSql .= "               ) acn                                                                                                                \n";
    $stSql .= "             , arrecadacao.calculo as ac                                                                                            \n";
    $stSql .= "     left join monetario.credito as mc                                                                                              \n";
    $stSql .= "            on mc.cod_credito = ac.cod_credito                                                                                      \n";
    $stSql .= "           and mc.cod_especie = ac.cod_especie                                                                                      \n";
    $stSql .= "           and mc.cod_genero = ac.cod_genero                                                                                        \n";
    $stSql .= "           and mc.cod_natureza = ac.cod_natureza                                                                                    \n";
    $stSql .= "     left join (                                                                                                                    \n";
    $stSql .= "                    select agv.cod_grupo                                                                                            \n";
    $stSql .= "                         , acgc.cod_calculo                                                                                         \n";
    $stSql .= "                         , agv.cod_vencimento                                                                                       \n";
    $stSql .= "                      from arrecadacao.calculo_grupo_credito as acgc                                                             \n";
    $stSql .= "                         , arrecadacao.calculo as ac                                                                                \n";
    $stSql .= "                         , arrecadacao.grupo_vencimento as agv                                                                      \n";
    $stSql .= "                     where ac.cod_calculo = acgc.cod_calculo                                                                        \n";
    $stSql .= "                       and acgc.cod_vencimento =  agv.cod_vencimento                                                                \n";
    $stSql .= "                       and acgc.cod_grupo =  agv.cod_grupo                                                                          \n";
    $stSql .= "               ) macg                                                                                                               \n";
    $stSql .= "on                                                                                                                                  \n";
    $stSql .= "    macg.cod_calculo    = ac.cod_calculo                                                                                            \n";
    $stSql .= "inner join                                                                                                                          \n";
    $stSql .= "    (select                                                                                                                         \n";
    $stSql .= "        ece.inscricao_economica,                                                                                                    \n";
    $stSql .= "        acec.cod_calculo,                                                                                                           \n";
    $stSql .= "        ea.cod_atividade,                                                                                                           \n";
    $stSql .= "        ea.cod_estrutural                                                                                                           \n";
    $stSql .= "     from                                                                                                                           \n";
    $stSql .= "        arrecadacao.cadastro_economico_calculo as acec,                                                                             \n";
    $stSql .= "        economico.cadastro_economico   as ece,                                                                                      \n";
    $stSql .= "        economico.atividade_cadastro_economico as eace,                                                                             \n";
    $stSql .= "        economico.atividade                    as ea                                                                                \n";
    $stSql .= "     where                                                                                                                          \n";
    $stSql .= "        acec.inscricao_economica = ece.inscricao_economica and                                                                      \n";
    $stSql .= "        ece.inscricao_economica  = eace.inscricao_economica and                                                                     \n";
    $stSql .= "        eace.cod_atividade       = ea.cod_atividade                                                                                 \n";
    $stSql .= "    )as aece                                                                                                                        \n";
    $stSql .= "on                                                                                                                                  \n";
    $stSql .= "    aece.cod_calculo = ac.cod_calculo                                                                                               \n";
    $stSql .= "where                                                                                                                               \n";
    $stSql .= "    ac.cod_calculo     = alc.cod_calculo   and                                                                                      \n";
    $stSql .= "    alc.cod_lancamento = al.cod_lancamento and                                                                                      \n";
    $stSql .= "    ap.cod_lancamento  = al.cod_lancamento and                                                                                      \n";
    $stSql .= "    cgm.numcgm         = al.numcgm         and                                                                                      \n";
    $stSql .= "    ac.numcgm          = cgm.numcgm        and                                                                                      \n";
    $stSql .= "    ap.cod_parcela     = acn.cod_parcela                                                                                            \n";

    return $stSql;
}
