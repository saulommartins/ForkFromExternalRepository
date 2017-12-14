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
    * Classe de mapeamento da tabela BENEFICIO.CONCESSAO_VALE_TRANSPORTE
    * Data de Criação: 11/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  BENEFICIO.CONCESSAO_VALE_TRANSPORTE
  * Data de Criação: 11/10/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TBeneficioConcessaoValeTransporte extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioConcessaoValeTransporte()
{
    parent::Persistente();
    $this->setTabela('beneficio.concessao_vale_transporte');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_concessao,cod_mes,exercicio');

    $this->AddCampo('cod_concessao','integer',true,'',true,false);
    $this->AddCampo('cod_mes','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,false);
    $this->AddCampo('cod_vale_transporte','integer',true,'',false,true);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    $this->AddCampo('quantidade','integer',true,'',false,false);
    $this->AddCampo('inicializado','boolean',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                                                       \n";
    $stSql .= "     bt.*,                                                                   \n";
    $stSql .= "     bc.cod_calendario,                                                      \n";
    $stSql .= "     mun_o.nom_municipio || '/' || blo.descricao || ' - ' ||  mun_d.nom_municipio || '/' || blo.descricao as vale_transporte,   \n";
    $stSql .= "     bv.cod_contrato                                                         \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "     beneficio.concessao_vale_transporte as bt                           \n";
    $stSql .= "LEFT JOIN                                                                    \n";
    $stSql .= "     beneficio.contrato_servidor_concessao_vale_transporte as bv         \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "         bt.cod_concessao  = bv.cod_concessao                                \n";
    $stSql .= "     AND bt.exercicio      = bv.exercicio                                    \n";
    $stSql .= "     AND bt.cod_mes        = bv.cod_mes                                      \n";
    $stSql .= "LEFT JOIN                                                                    \n";
    $stSql .= "     beneficio.concessao_vale_transporte_calendario as bc                    \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "         bt.cod_mes       = bc.cod_mes                                       \n";
    $stSql .= "     AND bt.exercicio     = bc.exercicio                                     \n";
    $stSql .= "     AND bt.cod_concessao = bc.cod_concessao,                                \n";
    $stSql .= "     beneficio.vale_transporte as bvt,                                       \n";
    $stSql .= "     beneficio.itinerario as bi                                              \n";
    $stSql .= "LEFT JOIN                                                                    \n";
    $stSql .= "     sw_municipio as mun_o                                                   \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "         bi.municipio_origem     = mun_o.cod_municipio                       \n";
    $stSql .= "     AND bi.uf_origem            = mun_o.cod_uf                              \n";

    $stSql .= "LEFT JOIN beneficio.linha blo                                           \n";
    $stSql .= "  ON  blo.cod_linha = bi.cod_linha_origem                               \n";
    $stSql .= "LEFT JOIN beneficio.linha bld                                           \n";
    $stSql .= "  ON  bld.cod_linha = bi.cod_linha_destino                              \n";

    $stSql .= "LEFT JOIN                                                                    \n";
    $stSql .= "     sw_municipio as mun_d                                                   \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "         bi.municipio_destino     = mun_d.cod_municipio                      \n";
    $stSql .= "     AND bi.uf_destino            = mun_d.cod_uf                             \n";
    $stSql .= "WHERE                                                                        \n";
    $stSql .= "         bvt.cod_vale_transporte = bi.vale_transporte_cod_vale_transporte    \n";
    $stSql .= "     AND bt.cod_vale_transporte        = bvt.cod_vale_transporte             \n";

    return $stSql;
}

function recuperaConcessoesCadastradasPorContrato(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaConcessoesCadastradasPorContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConcessoesCadastradasPorContrato()
{
    $stSql .= "SELECT                                                                       \n";
    $stSql .= "     bt.*,                                                                   \n";
    $stSql .= "     bc.registro,                                                            \n";
    $stSql .= "     bc.cod_contrato,                                                        \n";
    $stSql .= "     bc.grupo,                                                               \n";
    $stSql .= "     bc.cod_grupo,                                                           \n";
    $stSql .= "     bc.numcgm,                                                              \n";
    $stSql .= "     bc.nom_cgm,                                                             \n";
    $stSql .= "     TRIM(mes.descricao) as mes,                                             \n";
    $stSql .= "     to_char(bc.vigencia,'dd/mm/yyyy') as vigencia,                          \n";
    $stSql .= "     bvt.nom_municipio_o,                                                    \n";
    $stSql .= "     bvt.nom_municipio_d,                                                    \n";
    $stSql .= "     bvt.cod_linha_origem,                                                   \n";
    $stSql .= "     bvt.origem,                                                             \n";
    $stSql .= "     bvt.cod_linha_destino,                                                  \n";
    $stSql .= "     bvt.destino                                                             \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "     beneficio.concessao_vale_transporte as bt                               \n";
    $stSql .= "LEFT JOIN (                                                                  \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         mun_o.nom_municipio as nom_municipio_o,                             \n";
    $stSql .= "         mun_d.nom_municipio as nom_municipio_d,                             \n";
    $stSql .= "         blo.cod_linha as cod_linha_origem,                                  \n";
    $stSql .= "         bld.cod_linha as cod_linha_destino,                                 \n";
    $stSql .= "         blo.descricao as origem,                                            \n";
    $stSql .= "         bld.descricao as destino,                                           \n";
    $stSql .= "         bvt.cod_vale_transporte                                             \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         beneficio.vale_transporte as bvt,                                   \n";
    $stSql .= "         beneficio.itinerario as bi                                          \n";
    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "         sw_municipio as mun_o                                               \n";
    $stSql .= "     ON                                                                      \n";
    $stSql .= "             bi.municipio_origem     = mun_o.cod_municipio                   \n";
    $stSql .= "         AND bi.uf_origem            = mun_o.cod_uf                          \n";

    $stSql .= "     LEFT JOIN beneficio.linha blo                                           \n";
    $stSql .= "       ON  blo.cod_linha = bi.cod_linha_origem                               \n";
    $stSql .= "     LEFT JOIN beneficio.linha bld                                           \n";
    $stSql .= "       ON  bld.cod_linha = bi.cod_linha_destino                              \n";

    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "         sw_municipio as mun_d                                               \n";
    $stSql .= "     ON                                                                      \n";
    $stSql .= "             bi.municipio_destino     = mun_d.cod_municipio                  \n";
    $stSql .= "         AND bi.uf_destino            = mun_d.cod_uf                         \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         bvt.cod_vale_transporte = bi.vale_transporte_cod_vale_transporte) as bvt\n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "     bt.cod_vale_transporte = bvt.cod_vale_transporte                        \n";
    $stSql .= "LEFT JOIN                                                                    \n";
    $stSql .= "     administracao.mes as mes                                                          \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "     bt.cod_mes = mes.cod_mes                                                \n";
    $stSql .= "JOIN (                                                                       \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         pc.registro,                                                        \n";
    $stSql .= "         bcs.cod_contrato,                                                   \n";
    $stSql .= "         bcs.cod_concessao,                                                  \n";
    $stSql .= "         bcs.exercicio,                                                      \n";
    $stSql .= "         bcs.cod_mes,                                                        \n";
    $stSql .= "         bcs.vigencia,                                                       \n";
    $stSql .= "         pse.numcgm,                                                         \n";
    $stSql .= "         cgm.nom_cgm,                                                        \n";
    $stSql .= "         '' as grupo,                                                        \n";
    $stSql .= "         0 as cod_grupo                                                      \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         beneficio.contrato_servidor_concessao_vale_transporte as bcs,       \n";
    $stSql .= "         pessoal.contrato_servidor as ps,                                    \n";
    $stSql .= "         pessoal.servidor_contrato_servidor as pss,                          \n";
    $stSql .= "         pessoal.servidor as pse,                                            \n";
    $stSql .= "         sw_cgm_pessoa_fisica as pf,                                         \n";
    $stSql .= "         sw_cgm as cgm,                                                      \n";
    $stSql .= "         pessoal.contrato as pc                                              \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "             bcs.cod_contrato = ps.cod_contrato                              \n";
    $stSql .= "         AND ps.cod_contrato  = pc.cod_contrato                              \n";
    $stSql .= "         AND ps.cod_contrato  = pss.cod_contrato                             \n";
    $stSql .= "         AND pss.cod_servidor = pse.cod_servidor                             \n";
    $stSql .= "         AND pse.numcgm       = pf.numcgm                                    \n";
    $stSql .= "         AND pf.numcgm        = cgm.numcgm                                   \n";

    $stSql .= "     UNION                                                                   \n";

    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         pc.registro,                                                        \n";
    $stSql .= "         bgs.cod_contrato,                                                   \n";
    $stSql .= "         bgc.cod_concessao,                                                  \n";
    $stSql .= "         bgc.exercicio,                                                      \n";
    $stSql .= "         bgc.cod_mes,                                                        \n";
    $stSql .= "         bgc.vigencia,                                                       \n";
    $stSql .= "         pse.numcgm,                                                         \n";
    $stSql .= "         cgm.nom_cgm,                                                        \n";
    $stSql .= "         grupo.descricao as grupo,                                           \n";
    $stSql .= "         grupo.cod_grupo                                                     \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         beneficio.grupo_concessao_vale_transporte as bgc,                   \n";
    $stSql .= "         beneficio.grupo_concessao as grupo,                                 \n";
    $stSql .= "         beneficio.contrato_servidor_grupo_concessao_vale_transporte as bgs, \n";
    $stSql .= "         pessoal.contrato_servidor as ps,                                    \n";
    $stSql .= "         pessoal.servidor_contrato_servidor as pss,                          \n";
    $stSql .= "         pessoal.servidor as pse,                                            \n";
    $stSql .= "         sw_cgm_pessoa_fisica as pf,                                         \n";
    $stSql .= "         sw_cgm as cgm,                                                      \n";
    $stSql .= "         pessoal.contrato as pc                                              \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "             grupo.cod_grupo  = bgs.cod_grupo                                \n";
    $stSql .= "         AND bgc.cod_grupo    = grupo.cod_grupo                              \n";
    $stSql .= "         AND bgs.cod_contrato = ps.cod_contrato                              \n";
    $stSql .= "         AND ps.cod_contrato  = pc.cod_contrato                              \n";
    $stSql .= "         AND ps.cod_contrato  = pss.cod_contrato                             \n";
    $stSql .= "         AND pss.cod_servidor = pse.cod_servidor                             \n";
    $stSql .= "         AND pse.numcgm       = pf.numcgm                                    \n";
    $stSql .= "         AND pf.numcgm        = cgm.numcgm                                   \n";

    $stSql .= "         ) as bc                                                             \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "         bt.cod_concessao = bc.cod_concessao                                 \n";
    $stSql .= "     AND bt.exercicio     = bc.exercicio                                     \n";
    $stSql .= "     AND bt.cod_mes       = bc.cod_mes                                       \n";

    return $stSql;
}

function recuperaGruposCadastrados(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaGruposCadastrados().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaGruposCadastrados()
{
    $stSql .= "SELECT                                                                       \n";
    $stSql .= "     bt.cod_concessao,                                                       \n";
    $stSql .= "     bt.cod_mes,                                                             \n";
    $stSql .= "     bt.exercicio,                                                           \n";
    $stSql .= "     TRIM(bg.descricao) as grupo,                                            \n";
    $stSql .= "     bg.cod_grupo,                                                           \n";
    $stSql .= "     TRIM(mes.descricao) as mes,                                             \n";
    $stSql .= "     to_char(bg.vigencia,'dd/mm/yyyy') as vigencia,                          \n";
    $stSql .= "     bvt.nom_municipio_o,                                                    \n";
    $stSql .= "     bvt.nom_municipio_d,                                                    \n";
    $stSql .= "     bvt.cod_linha_origem,                                                   \n";
    $stSql .= "     bvt.origem,                                                             \n";
    $stSql .= "     bvt.cod_linha_destino,                                                  \n";
    $stSql .= "     bvt.destino,                                                             \n";

    $stSql .= "     bvt.nom_municipio_o || '/' || bvt.origem || ' - ' || bvt.nom_municipio_d || '/' || bvt.destino as vale_transporte    \n";

    $stSql .= "FROM                                                                         \n";
    $stSql .= "     beneficio.concessao_vale_transporte as bt                           \n";
    $stSql .= "LEFT JOIN (                                                                  \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         mun_o.nom_municipio as nom_municipio_o,                             \n";
    $stSql .= "         mun_d.nom_municipio as nom_municipio_d,                             \n";
    $stSql .= "         blo.cod_linha as cod_linha_origem,                                  \n";
    $stSql .= "         bld.cod_linha as cod_linha_destino,                                 \n";
    $stSql .= "         blo.descricao as origem,                                            \n";
    $stSql .= "         bld.descricao as destino,                                           \n";
    $stSql .= "         bvt.cod_vale_transporte                                             \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         beneficio.vale_transporte as bvt,                               \n";
    $stSql .= "         beneficio.itinerario as bi                                      \n";
    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "         sw_municipio as mun_o                                              \n";
    $stSql .= "     ON                                                                      \n";
    $stSql .= "             bi.municipio_origem     = mun_o.cod_municipio                   \n";
    $stSql .= "         AND bi.uf_origem            = mun_o.cod_uf                          \n";

    $stSql .= "     LEFT JOIN beneficio.linha blo                                           \n";
    $stSql .= "       ON  blo.cod_linha = bi.cod_linha_origem                               \n";
    $stSql .= "     LEFT JOIN beneficio.linha bld                                           \n";
    $stSql .= "       ON  bld.cod_linha = bi.cod_linha_destino                              \n";

    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "         sw_municipio as mun_d                                              \n";
    $stSql .= "     ON                                                                      \n";
    $stSql .= "             bi.municipio_destino    = mun_d.cod_municipio                   \n";
    $stSql .= "         AND bi.uf_destino           = mun_d.cod_uf                          \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         bvt.cod_vale_transporte = bi.vale_transporte_cod_vale_transporte) as bvt\n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "     bt.cod_vale_transporte = bvt.cod_vale_transporte                        \n";
    $stSql .= "JOIN (                                                                       \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         bgc.cod_concessao,                                                  \n";
    $stSql .= "         bgc.exercicio,                                                      \n";
    $stSql .= "         bgc.cod_mes,                                                        \n";
    $stSql .= "         bgc.vigencia,                                                       \n";
    $stSql .= "         bgr.cod_grupo,                                                      \n";
    $stSql .= "         bgr.descricao,                                                      \n";
    $stSql .= "         bcg.cod_contrato                                                    \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         beneficio.grupo_concessao_vale_transporte as bgc,               \n";
    $stSql .= "         beneficio.grupo_concessao as bgr                                \n";
    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "         beneficio.contrato_servidor_grupo_concessao_vale_transporte as bcg \n";
    $stSql .= "     ON                                                                      \n";
    $stSql .= "         bcg.cod_grupo = bgr.cod_grupo                                       \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         bgc.cod_grupo = bgr.cod_grupo) as bg                                \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "         bt.cod_concessao = bg.cod_concessao                                 \n";
    $stSql .= "     AND bt.exercicio     = bg.exercicio                                     \n";
    $stSql .= "     AND bt.cod_mes       = bg.cod_mes                                       \n";
    $stSql .= "LEFT JOIN                                                                    \n";
    $stSql .= "     administracao.mes as mes                                                          \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "     bt.cod_mes = mes.cod_mes                                                \n";

    return $stSql;
}

function recuperaValesTransportesCadastrados(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaValesTransportesCadastrados().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValesTransportesCadastrados()
{
    $stSql .= "SELECT                                                                       \n";
    $stSql .= "     bt.cod_concessao,                                                       \n";
    $stSql .= "     bt.cod_vale_transporte,                                                 \n";
    $stSql .= "     bt.exercicio,                                                           \n";
    $stSql .= "     bt.cod_mes,                                                             \n";
    $stSql .= "     bc.registro,                                                            \n";
    $stSql .= "     TRIM(mes.descricao) as mes,                                             \n";
    $stSql .= "     to_char(bc.vigencia,'dd/mm/yyyy') as vigencia,                          \n";
    $stSql .= "     bvt.nom_municipio_o || '/' || bvt.nom_municipio_d as vale_transporte    \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "     beneficio.concessao_vale_transporte as bt                           \n";

    $stSql .= "JOIN (                                                                       \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         pc.registro,                                                        \n";
    $stSql .= "         bcs.cod_concessao,                                                  \n";
    $stSql .= "         bcs.exercicio,                                                      \n";
    $stSql .= "         bcs.cod_mes,                                                        \n";
    $stSql .= "         bcs.vigencia,                                                       \n";
    $stSql .= "         pse.numcgm,                                                         \n";
    $stSql .= "         cgm.nom_cgm,                                                        \n";
    $stSql .= "         '' as grupo                                                         \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         beneficio.contrato_servidor_concessao_vale_transporte as bcs,   \n";
    $stSql .= "         pessoal.contrato_servidor as ps,                                \n";
    $stSql .= "         pessoal.servidor_contrato_servidor as pss,                      \n";
    $stSql .= "         pessoal.servidor as pse,                                        \n";
    $stSql .= "         sw_cgm_pessoa_fisica as pf,                                        \n";
    $stSql .= "         sw_cgm as cgm,                                                     \n";
    $stSql .= "         pessoal.contrato as pc                                          \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "             bcs.cod_contrato = ps.cod_contrato                              \n";
    $stSql .= "         AND ps.cod_contrato  = pc.cod_contrato                              \n";
    $stSql .= "         AND ps.cod_contrato  = pss.cod_contrato                             \n";
    $stSql .= "         AND pss.cod_servidor = pse.cod_servidor                             \n";
    $stSql .= "         AND pse.numcgm       = pf.numcgm                                    \n";
    $stSql .= "         AND pf.numcgm        = cgm.numcgm                                   \n";
    $stSql .= "         ) as bc                                                             \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "         bt.cod_concessao = bc.cod_concessao                                 \n";
    $stSql .= "     AND bt.exercicio     = bc.exercicio                                     \n";
    $stSql .= "     AND bt.cod_mes       = bc.cod_mes                                       \n";

    $stSql .= "LEFT JOIN (                                                                  \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         mun_o.nom_municipio as nom_municipio_o,                             \n";
    $stSql .= "         mun_d.nom_municipio as nom_municipio_d,                             \n";
    $stSql .= "         bvt.cod_vale_transporte                                             \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         beneficio.vale_transporte as bvt,                               \n";
    $stSql .= "         beneficio.itinerario as bi                                      \n";
    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "         sw_municipio as mun_o                                              \n";
    $stSql .= "     ON                                                                      \n";
    $stSql .= "             bi.municipio_origem     = mun_o.cod_municipio                   \n";
    $stSql .= "         AND bi.uf_origem            = mun_o.cod_uf                          \n";
    $stSql .= "     LEFT JOIN                                                               \n";
    $stSql .= "         sw_municipio as mun_d                                              \n";
    $stSql .= "     ON                                                                      \n";
    $stSql .= "             bi.municipio_destino    = mun_d.cod_municipio                   \n";
    $stSql .= "         AND bi.uf_destino           = mun_d.cod_uf                          \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         bvt.cod_vale_transporte = bi.vale_transporte_cod_vale_transporte) as bvt\n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "     bt.cod_vale_transporte = bvt.cod_vale_transporte                        \n";
    $stSql .= "LEFT JOIN                                                                    \n";
    $stSql .= "     administracao.mes as mes                                                          \n";
    $stSql .= "ON                                                                           \n";
    $stSql .= "     bt.cod_mes = mes.cod_mes                                                \n";

    return $stSql;
}

function recuperaConcessaoValeTransporte(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaConcessaoValeTransporte().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConcessaoValeTransporte()
{
    $stSql .= "    SELECT Bcvt.*                                                            \n";
    $stSql .= "         , Bcvts.cod_dia                                                     \n";
    $stSql .= "         , Bcvts.quantidade AS qtd_dia                                       \n";
    $stSql .= "         , Bcvts.obrigatorio AS obrigatorio                                  \n";
    $stSql .= "         , Bcvtc.cod_calendario                                              \n";
    $stSql .= "      FROM beneficio.concessao_vale_transporte AS Bcvt                   \n";
    $stSql .= " LEFT JOIN beneficio.concessao_vale_transporte_semanal AS Bcvts          \n";
    $stSql .= "        ON Bcvts.cod_concessao = Bcvt.cod_concessao                          \n";
    $stSql .= "       AND Bcvts.cod_mes = Bcvt.cod_mes                                      \n";
    $stSql .= "       AND Bcvts.exercicio = Bcvt.exercicio                                  \n";
    $stSql .= " LEFT JOIN beneficio.concessao_vale_transporte_calendario AS Bcvtc       \n";
    $stSql .= "        ON Bcvtc.cod_concessao = Bcvt.cod_concessao                          \n";
    $stSql .= "       AND Bcvtc.cod_mes = Bcvt.cod_mes                                      \n";
    $stSql .= "       AND Bcvtc.exercicio = Bcvt.exercicio                                  \n";

    return $stSql;
}

function recuperaTotaisPorFornecedor(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaTotaisPorFornecedor().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaTotaisPorFornecedor()
{
    $stSql .= "   SELECT *                                                                                                            \n";
    $stSql .= "     FROM (                                                                                                            \n";
    $stSql .= "          SELECT Bvt.fornecedor_vale_transporte_fornecedor_numcgm AS numcgm                                            \n";
    $stSql .= "               , Cgm.nom_cgm                                                                                           \n";
    $stSql .= "               , Bcvt.cod_concessao                                                                                    \n";
    $stSql .= "               , Bcvt.exercicio                                                                                        \n";
    $stSql .= "               , Bcvt.cod_mes                                                                                          \n";
    $stSql .= "               , Mun1.nom_municipio||'-'||Bl1.descricao||'/'||Mun2.nom_municipio||'-'||Bl2.descricao AS itinerario     \n";
    $stSql .= "               , Bvt.cod_vale_transporte                                                                               \n";
    $stSql .= "               , Bc.valor AS valor_unitario                                                                            \n";
    $stSql .= "               , TO_CHAR(Bc.inicio_vigencia,'dd/mm/yyyy') AS vigencia                                                  \n";
    $stSql .= "               , Bcvt.quantidade AS quantidade_por_contrato                                                            \n";
    $stSql .= "               , Bc.valor * SUM(Bcvt.quantidade) AS valor_total                                                        \n";
    $stSql .= "               , DadosContrato.cod_local                                                                               \n";
    $stSql .= "               , DadosContrato.desc_local                                                                              \n";
    $stSql .= "               , DadosContrato.cod_orgao                                                                               \n";
    $stSql .= "               , DadosContrato.desc_orgao                                                                              \n";
    $stSql .= "               , SUM(Bcvt.quantidade) AS total_fornecedor_mes                                                          \n";
    $stSql .= "            FROM beneficio.concessao_vale_transporte AS Bcvt                                                           \n";
    $stSql .= "            JOIN beneficio.vale_transporte AS Bvt                                                                      \n";
    $stSql .= "              ON Bcvt.cod_vale_transporte = Bvt.cod_vale_transporte                                                    \n";
    $stSql .= "            JOIN sw_cgm AS Cgm                                                                                         \n";
    $stSql .= "              ON Cgm.numcgm = Bvt.fornecedor_vale_transporte_fornecedor_numcgm                                         \n";
    $stSql .= "            JOIN beneficio.itinerario AS Bi                                                                            \n";
    $stSql .= "              ON Bi.vale_transporte_cod_vale_transporte = Bvt.cod_vale_transporte                                      \n";
    $stSql .= "            JOIN sw_municipio AS Mun1                                                                                  \n";
    $stSql .= "              ON Mun1.cod_municipio = Bi.municipio_origem                                                              \n";
    $stSql .= "             AND Mun1.cod_uf        = Bi.uf_origem                                                                     \n";
    $stSql .= "            JOIN sw_municipio AS Mun2                                                                                  \n";
    $stSql .= "              ON Mun2.cod_municipio = Bi.municipio_destino                                                             \n";
    $stSql .= "             AND Mun2.cod_uf        = Bi.uf_destino                                                                    \n";
    $stSql .= "            JOIN beneficio.linha AS Bl1                                                                                \n";
    $stSql .= "              ON Bl1.cod_linha = Bi.cod_linha_origem                                                                   \n";
    $stSql .= "            JOIN beneficio.linha AS Bl2                                                                                \n";
    $stSql .= "              ON Bl2.cod_linha = Bi.cod_linha_destino                                                                  \n";
    $stSql .= "            JOIN ( SELECT Bc1.*                                                                                        \n";
    $stSql .= "                     FROM beneficio.custo AS Bc1                                                                       \n";
    $stSql .= "                        , ( SELECT MAX(inicio_vigencia) AS inicio_vigencia                                             \n";
    $stSql .= "                                   , vale_transporte_cod_vale_transporte                                               \n";
    $stSql .= "                                FROM beneficio.custo                                                                   \n";
    if ($dtVigencia = $this->getDado('dtVigencia'))
        $stSql .= "                           WHERE inicio_vigencia <= TO_DATE('".$dtVigencia."','dd/mm/yyyy')                        \n";
    $stSql .= "                            GROUP BY vale_transporte_cod_vale_transporte                                               \n";
    $stSql .= "                          ) AS MaxCusto                                                                                \n";
    $stSql .= "                    WHERE MaxCusto.inicio_vigencia = Bc1.inicio_vigencia                                               \n";
    $stSql .= "                      AND MaxCusto.vale_transporte_cod_vale_transporte = Bc1.vale_transporte_cod_vale_transporte       \n";
    $stSql .= "                 ) AS Bc                                                                                               \n";
    $stSql .= "              ON Bc.vale_transporte_cod_vale_transporte = Bvt.cod_vale_transporte                                      \n";
    $stSql .= "            JOIN ( SELECT PSCS.cod_servidor                                                                            \n";
    $stSql .= "                        , PSCS.cod_contrato                                                                            \n";
    $stSql .= "                        , PSCS.registro                                                                                \n";
    $stSql .= "                        , PS.numcgm                                                                                    \n";
    $stSql .= "                        , CGM.nom_cgm                                                                                  \n";
    $stSql .= "                        , PSCS.cod_orgao                                                                               \n";
    $stSql .= "                        , PSCS.desc_orgao                                                                              \n";
    $stSql .= "                        , PSCS.cod_local                                                                               \n";
    $stSql .= "                        , PSCS.desc_local                                                                              \n";
    $stSql .= "                        , PSCS.cod_concessao                                                                           \n";
    $stSql .= "                        , PSCS.exercicio                                                                               \n";
    $stSql .= "                        , PSCS.cod_mes                                                                                 \n";
    $stSql .= "                     FROM (   SELECT PSCS.cod_servidor                                                                 \n";
    $stSql .= "                                   , PSCS.cod_contrato                                                                 \n";
    $stSql .= "                                   , PC.registro                                                                       \n";
    $stSql .= "                                   , PCSO.cod_orgao                                                                    \n";
    $stSql .= "                                   , OO.orgao                                                                          \n";
    $stSql .= "                                   , recuperaDescricaoOrgao(PCSO.cod_orgao, '".Sessao::getExercicio()."-01-01') AS desc_orgao                                                        \n";
    $stSql .= "                                   , OL.cod_local                                                                      \n";
    $stSql .= "                                   , OL.descricao AS desc_local                                                        \n";
    $stSql .= "                                   , BCSCVT.cod_concessao                                                              \n";
    $stSql .= "                                   , BCSCVT.exercicio                                                                  \n";
    $stSql .= "                                   , BCSCVT.cod_mes                                                                    \n";
    $stSql .= "                                FROM beneficio.contrato_servidor_concessao_vale_transporte BCSCVT                      \n";
    $stSql .= "                                JOIN pessoal.contrato PC                                                               \n";
    $stSql .= "                                  ON PC.cod_contrato    = BCSCVT.cod_contrato                                          \n";
    $stSql .= "                                JOIN pessoal.servidor_contrato_servidor PSCS                                           \n";
    $stSql .= "                                  ON PSCS.cod_contrato  = BCSCVT.cod_contrato                                          \n";
    $stSql .= "                                JOIN (    SELECT MAXPCSO.cod_contrato                                                  \n";
    $stSql .= "                                               , MAX(MAXPCSO.timestamp) AS timestamp                                   \n";
    $stSql .= "                                           FROM pessoal.contrato_servidor_orgao MAXPCSO                                \n";
    $stSql .= "                                       GROUP BY MAXPCSO.cod_contrato ) AS MPCSO                                        \n";
    $stSql .= "                                  ON MPCSO.cod_contrato = BCSCVT.cod_contrato                                          \n";
    $stSql .= "                                JOIN pessoal.contrato_servidor_orgao PCSO                                              \n";
    $stSql .= "                                  ON PCSO.cod_contrato  = MPCSO.cod_contrato                                           \n";
    $stSql .= "                                 AND PCSO.timestamp     = MPCSO.timestamp                                              \n";
    $stSql .= "                                JOIN organograma.vw_orgao_nivel AS OO                                                  \n";
    $stSql .= "                                  ON OO.cod_orgao       = PCSO.cod_orgao                                               \n";
    $stSql .= "                           LEFT JOIN (  SELECT MAXPCSL.cod_contrato                                                    \n";
    $stSql .= "                                             , MAX(MAXPCSL.timestamp) AS timestamp                                     \n";
    $stSql .= "                                          FROM pessoal.contrato_servidor_local MAXPCSL                                 \n";
    $stSql .= "                                      GROUP BY MAXPCSL.cod_contrato ) AS MPCSL                                         \n";
    $stSql .= "                                  ON MPCSL.cod_contrato = BCSCVT.cod_contrato                                          \n";
    $stSql .= "                           LEFT JOIN pessoal.contrato_servidor_local PCSL                                              \n";
    $stSql .= "                                  ON PCSL.cod_contrato  = MPCSL.cod_contrato                                           \n";
    $stSql .= "                                 AND PCSL.timestamp     = MPCSL.timestamp                                              \n";
    $stSql .= "                           LEFT JOIN organograma.\"local\" AS OL                                                       \n";
    $stSql .= "                                  ON OL.cod_local       = PCSL.cod_local                                               \n";
    $stSql .= "                                                                                                                       \n";
    $stSql .= "                               UNION                                                                                   \n";
    $stSql .= "                                                                                                                       \n";
    $stSql .= "                              SELECT PSCS.cod_servidor                                                                 \n";
    $stSql .= "                                   , PSCS.cod_contrato                                                                 \n";
    $stSql .= "                                   , PC.registro                                                                       \n";
    $stSql .= "                                   , PCSO.cod_orgao                                                                    \n";
    $stSql .= "                                   , OO.orgao                                                                          \n";
    $stSql .= "                                   , recuperaDescricaoOrgao(PCSO.cod_orgao, '".Sessao::getExercicio()."-01-01') AS desc_orgao                                                        \n";
    $stSql .= "                                   , OL.cod_local                                                                      \n";
    $stSql .= "                                   , OL.descricao AS desc_local                                                        \n";
    $stSql .= "                                   , BGCVT.cod_concessao                                                               \n";
    $stSql .= "                                   , BGCVT.exercicio                                                                   \n";
    $stSql .= "                                   , BGCVT.cod_mes                                                                     \n";
    $stSql .= "                                FROM beneficio.grupo_concessao_vale_transporte AS BGCVT                                \n";
    $stSql .= "                                JOIN beneficio.contrato_servidor_grupo_concessao_vale_transporte BCSGCVT               \n";
    $stSql .= "                                  ON BGCVT.cod_grupo = BCSGCVT.cod_grupo                                               \n";
    $stSql .= "                                JOIN pessoal.contrato PC                                                               \n";
    $stSql .= "                                  ON PC.cod_contrato    = BCSGCVT.cod_contrato                                         \n";
    $stSql .= "                                JOIN pessoal.servidor_contrato_servidor PSCS                                           \n";
    $stSql .= "                                  ON PSCS.cod_contrato  = BCSGCVT.cod_contrato                                         \n";
    $stSql .= "                                JOIN (  SELECT MAXPCSO.cod_contrato                                                    \n";
    $stSql .= "                                             , MAX(MAXPCSO.timestamp) as timestamp                                     \n";
    $stSql .= "                                          FROM pessoal.contrato_servidor_orgao MAXPCSO                                 \n";
    $stSql .= "                                      GROUP BY MAXPCSO.cod_contrato ) as MPCSO                                         \n";
    $stSql .= "                                  ON MPCSO.cod_contrato = BCSGCVT.cod_contrato                                         \n";
    $stSql .= "                                JOIN pessoal.contrato_servidor_orgao PCSO                                              \n";
    $stSql .= "                                  ON PCSO.cod_contrato  = MPCSO.cod_contrato                                           \n";
    $stSql .= "                                 AND PCSO.timestamp     = MPCSO.timestamp                                              \n";
    $stSql .= "                                JOIN organograma.vw_orgao_nivel AS OO                                                  \n";
    $stSql .= "                                  ON OO.cod_orgao       = PCSO.cod_orgao                                               \n";
    $stSql .= "                           LEFT JOIN (  SELECT MAXPCSL.cod_contrato                                                    \n";
    $stSql .= "                                             , MAX(MAXPCSL.timestamp) as timestamp                                     \n";
    $stSql .= "                                          FROM pessoal.contrato_servidor_local MAXPCSL                                 \n";
    $stSql .= "                                      GROUP BY MAXPCSL.cod_contrato ) as MPCSL                                         \n";
    $stSql .= "                                  ON MPCSL.cod_contrato = BCSGCVT.cod_contrato                                         \n";
    $stSql .= "                           LEFT JOIN pessoal.contrato_servidor_local PCSL                                              \n";
    $stSql .= "                                  ON PCSL.cod_contrato  = MPCSL.cod_contrato                                           \n";
    $stSql .= "                                 AND PCSL.timestamp     = MPCSL.timestamp                                              \n";
    $stSql .= "                           LEFT JOIN organograma.\"local\" AS OL                                                       \n";
    $stSql .= "                                  ON OL.cod_local       = PCSL.cod_local                                               \n";
    $stSql .= "                         ) AS PSCS                                                                                     \n";
    $stSql .= "                     JOIN pessoal.servidor PS                                                                          \n";
    $stSql .= "                       ON PS.cod_servidor = PSCS.cod_servidor                                                          \n";
    $stSql .= "                     JOIN sw_cgm CGM                                                                                   \n";
    $stSql .= "                       ON CGM.numcgm      = PS.numcgm                                                                  \n";
    $stSql .= "                 ) AS DadosContrato                                                                                    \n";
    $stSql .= "              ON DadosContrato.cod_concessao = Bcvt.cod_concessao                                                      \n";
    $stSql .= "             AND DadosContrato.exercicio     = Bcvt.exercicio                                                          \n";
    $stSql .= "             AND DadosContrato.cod_mes       = Bcvt.cod_mes                                                            \n";
    $stSql .= "        GROUP BY Bcvt.cod_concessao                                                                                    \n";
    $stSql .= "               , Bcvt.exercicio                                                                                        \n";
    $stSql .= "               , BCvt.cod_mes                                                                                          \n";
    $stSql .= "               , Bvt.fornecedor_vale_transporte_fornecedor_numcgm                                                      \n";
    $stSql .= "               , Cgm.nom_cgm                                                                                           \n";
    $stSql .= "               , Mun1.nom_municipio                                                                                    \n";
    $stSql .= "               , Mun2.nom_municipio                                                                                    \n";
    $stSql .= "               , Bl1.descricao                                                                                         \n";
    $stSql .= "               , Bl2.descricao                                                                                         \n";
    $stSql .= "               , Bvt.cod_vale_transporte                                                                               \n";
    $stSql .= "               , Bc.valor                                                                                              \n";
    $stSql .= "               , Bc.inicio_vigencia                                                                                    \n";
    $stSql .= "               , Bcvt.quantidade                                                                                       \n";
    $stSql .= "               , DadosContrato.cod_local                                                                               \n";
    $stSql .= "               , DadosContrato.desc_local                                                                              \n";
    $stSql .= "               , DadosContrato.cod_orgao                                                                               \n";
    $stSql .= "               , DadosContrato.desc_orgao                                                                              \n";
    $stSql .= "        ORDER BY Cgm.nom_cgm                                                                                           \n";
    $stSql .= "               , Bcvt.exercicio                                                                                        \n";
    $stSql .= "               , Bcvt.cod_mes                                                                                          \n";
    $stSql .= "               , Bvt.cod_vale_transporte                                                                               \n";
    $stSql .= "               , Bcvt.cod_concessao                                                                                    \n";
    $stSql .= "          ) AS tabela                                                                                                  \n";

    return $stSql;
}

function montaRecuperaVTCalculado()
{
    $stSql  = "    SELECT evento_calculado.*                                                                                        \n";
    $stSql .= "      FROM folhapagamento.registro_evento_periodo                                           \n";
    $stSql .= "INNER JOIN folhapagamento.registro_evento                                                   \n";
    $stSql .= "        ON registro_evento_periodo.cod_registro = registro_evento.cod_registro                                       \n";
    $stSql .= "INNER JOIN folhapagamento.ultimo_registro_evento                                            \n";
    $stSql .= "        ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                        \n";
    $stSql .= "       AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                                            \n";
    $stSql .= "       AND registro_evento.timestamp = ultimo_registro_evento.timestamp                                              \n";
    $stSql .= "INNER JOIN folhapagamento.evento_calculado                                                  \n";
    $stSql .= "        ON ultimo_registro_evento.cod_registro = evento_calculado.cod_registro                                       \n";
    $stSql .= "       AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento                                           \n";
    $stSql .= "       AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro                                    \n";
    $stSql .= "     WHERE registro_evento_periodo.cod_contrato = ".$this->getDado("cod_contrato")."                                 \n";
    $stSql .= "       AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."         \n";
    $stSql .= "       AND registro_evento.cod_evento = ( SELECT beneficio_evento.cod_evento                                         \n";
    $stSql .= "                                            FROM folhapagamento.beneficio_evento            \n";
    $stSql .= "                                      INNER JOIN ( SELECT cod_evento                                                 \n";
    $stSql .= "                                                        , MAX(timestamp) as timestamp                                \n";
    $stSql .= "                                                     FROM folhapagamento.beneficio_evento   \n";
    $stSql .= "                                                 GROUP BY cod_evento) as max_beneficio_evento                        \n";
    $stSql .= "                                              ON beneficio_evento.cod_evento = max_beneficio_evento.cod_evento       \n";
    $stSql .= "                                             AND beneficio_evento.timestamp = max_beneficio_evento.timestamp )       \n";

    return $stSql;
}

function recuperaVTCalculado(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaRecuperaVTCalculado",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function recuperaConcessaoValeTransporteRelatorio(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaConcessaoValeTransporteRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaConcessaoValeTransporteRelatorio()
{
    $stSql .= "   SELECT *                                                                                                                                                       \n";
    $stSql .= "   FROM (   SELECT BCVT.cod_mes           AS mes                                                                                                                  \n";
    $stSql .= "                 , BCVT.exercicio         AS exercicio                                                                                                            \n";
    $stSql .= "                 , BTCVT.descricao        AS tipo                                                                                                                 \n";
    $stSql .= "                 , MUN_ORIGEM.nom_municipio || '-' || BL_ORIGEM.descricao || ' / ' || MUN_DESTINO.nom_municipio || '-' || BL_DESTINO.descricao  AS itinerario     \n";
    $stSql .= "                 , BCVT.quantidade        AS quantidade                                                                                                           \n";
    $stSql .= "                 , CGM_FORNECEDOR.nom_cgm AS fornecedor                                                                                                           \n";
    $stSql .= "                 , DADOS_CONTRATO.numcgm                                                                                                                          \n";
    $stSql .= "                 , DADOS_CONTRATO.nom_cgm                                                                                                                         \n";
    $stSql .= "                 , DADOS_CONTRATO.registro                                                                                                                        \n";
    $stSql .= "                 , DADOS_CONTRATO.orgao                                                                                                                           \n";
    $stSql .= "                 , DADOS_CONTRATO.cod_orgao                                                                                                                       \n";
    $stSql .= "                 , DADOS_CONTRATO.desc_orgao                                                                                                                      \n";
    $stSql .= "                 , DADOS_CONTRATO.cod_local                                                                                                                       \n";
    $stSql .= "                 , DADOS_CONTRATO.desc_local                                                                                                                      \n";
    $stSql .= "                 , DADOS_CONTRATO.cod_contrato                                                                                                                    \n";
    $stSql .= "                 , DADOS_CONTRATO.desc_grupo                                                                                                                      \n";
    $stSql .= "                 , DADOS_CONTRATO.cod_grupo                                                                                                                       \n";
    $stSql .= "                 , BCVT.cod_concessao                                                                                                                             \n";
    $stSql .= "              FROM beneficio.concessao_vale_transporte BCVT                                                                                                       \n";
    $stSql .= "              JOIN ( SELECT PSCS.cod_servidor                                                                                                                     \n";
    $stSql .= "                          , PSCS.cod_contrato                                                                                                                     \n";
    $stSql .= "                          , PSCS.registro                                                                                                                         \n";
    $stSql .= "                          , PS.numcgm                                                                                                                             \n";
    $stSql .= "                          , CGM.nom_cgm                                                                                                                           \n";
    $stSql .= "                          , PSCS.orgao                                                                                                                            \n";
    $stSql .= "                          , PSCS.desc_orgao                                                                                                                       \n";
    $stSql .= "                          , PSCS.cod_local                                                                                                                        \n";
    $stSql .= "                          , PSCS.desc_local                                                                                                                       \n";
    $stSql .= "                          , PSCS.cod_concessao                                                                                                                    \n";
    $stSql .= "                          , PSCS.exercicio                                                                                                                        \n";
    $stSql .= "                          , PSCS.cod_mes                                                                                                                          \n";
    $stSql .= "                          , PSCS.desc_grupo                                                                                                                       \n";
    $stSql .= "                          , PSCS.cod_grupo                                                                                                                        \n";
    $stSql .= "                          , PSCS.cod_orgao                                                                                                                        \n";
    $stSql .= "                      FROM (    SELECT PSCS.cod_servidor                                                                                                          \n";
    $stSql .= "                                     , PSCS.cod_contrato                                                                                                          \n";
    $stSql .= "                                     , PC.registro                                                                                                                \n";
    $stSql .= "                                     , PCSO.cod_orgao                                                                                                             \n";
    $stSql .= "                                     , OO.orgao                                                                                                                   \n";
    $stSql .= "                                     , recuperaDescricaoOrgao(PCSO.cod_orgao, '".Sessao::getExercicio()."-01-01') AS desc_orgao                                                                                                 \n";
    $stSql .= "                                     , OL.cod_local                                                                                                               \n";
    $stSql .= "                                     , OL.descricao AS desc_local                                                                                                 \n";
    $stSql .= "                                     , BCSCVT.cod_concessao                                                                                                       \n";
    $stSql .= "                                     , BCSCVT.exercicio                                                                                                           \n";
    $stSql .= "                                     , BCSCVT.cod_mes                                                                                                             \n";
    $stSql .= "                                     , null AS desc_grupo                                                                                                         \n";
    $stSql .= "                                     , null AS cod_grupo                                                                                                          \n";
    $stSql .= "                                  FROM beneficio.contrato_servidor_concessao_vale_transporte BCSCVT                                                               \n";
    $stSql .= "                                  JOIN pessoal.contrato PC                                                                                                        \n";
    $stSql .= "                                    ON PC.cod_contrato    = BCSCVT.cod_contrato                                                                                   \n";
    $stSql .= "                                  JOIN pessoal.servidor_contrato_servidor PSCS                                                                                    \n";
    $stSql .= "                                    ON PSCS.cod_contrato  = BCSCVT.cod_contrato                                                                                   \n";
    $stSql .= "                                  JOIN (    SELECT MAXPCSO.cod_contrato                                                                                           \n";
    $stSql .= "                                                 , MAX(MAXPCSO.timestamp) AS timestamp                                                                            \n";
    $stSql .= "                                             FROM pessoal.contrato_servidor_orgao MAXPCSO                                                                         \n";
    $stSql .= "                                         GROUP BY MAXPCSO.cod_contrato ) AS MPCSO                                                                                 \n";
    $stSql .= "                                    ON MPCSO.cod_contrato = BCSCVT.cod_contrato                                                                                   \n";
    $stSql .= "                                  JOIN pessoal.contrato_servidor_orgao PCSO                                                                                       \n";
    $stSql .= "                                    ON PCSO.cod_contrato  = MPCSO.cod_contrato                                                                                    \n";
    $stSql .= "                                   AND PCSO.timestamp     = MPCSO.timestamp                                                                                       \n";
    $stSql .= "                                  JOIN organograma.vw_orgao_nivel AS OO                                                                                           \n";
    $stSql .= "                                    ON OO.cod_orgao       = PCSO.cod_orgao                                                                                        \n";
    $stSql .= "                             LEFT JOIN (  SELECT MAXPCSL.cod_contrato                                                                                             \n";
    $stSql .= "                                               , MAX(MAXPCSL.timestamp) AS timestamp                                                                              \n";
    $stSql .= "                                            FROM pessoal.contrato_servidor_local MAXPCSL                                                                          \n";
    $stSql .= "                                        GROUP BY MAXPCSL.cod_contrato ) AS MPCSL                                                                                  \n";
    $stSql .= "                                    ON MPCSL.cod_contrato = BCSCVT.cod_contrato                                                                                   \n";
    $stSql .= "                             LEFT JOIN pessoal.contrato_servidor_local PCSL                                                                                       \n";
    $stSql .= "                                    ON PCSL.cod_contrato  = MPCSL.cod_contrato                                                                                    \n";
    $stSql .= "                                   AND PCSL.timestamp     = MPCSL.timestamp                                                                                       \n";
    $stSql .= "                             LEFT JOIN organograma.\"local\" AS OL                                                                                                \n";
    $stSql .= "                                    ON OL.cod_local       = PCSL.cod_local                                                                                        \n";
    $stSql .= "                                                                                                                                                                  \n";
    $stSql .= "                                 UNION                                                                                                                            \n";
    $stSql .= "                                                                                                                                                                  \n";
    $stSql .= "                                SELECT PSCS.cod_servidor                                                                                                          \n";
    $stSql .= "                                     , PSCS.cod_contrato                                                                                                          \n";
    $stSql .= "                                     , PC.registro                                                                                                                \n";
    $stSql .= "                                     , PCSO.cod_orgao                                                                                                             \n";
    $stSql .= "                                     , OO.orgao                                                                                                                   \n";
    $stSql .= "                                     , recuperaDescricaoOrgao(PCSO.cod_orgao, '".Sessao::getExercicio()."-01-01') AS desc_orgao                                   \n";
    $stSql .= "                                     , OL.cod_local                                                                                                               \n";
    $stSql .= "                                     , OL.descricao AS desc_local                                                                                                 \n";
    $stSql .= "                                     , BGCVT.cod_concessao                                                                                                        \n";
    $stSql .= "                                     , BGCVT.exercicio                                                                                                            \n";
    $stSql .= "                                     , BGCVT.cod_mes                                                                                                              \n";
    $stSql .= "                                     , BGC.descricao AS desc_grupo                                                                                                \n";
    $stSql .= "                                     , BGC.cod_grupo                                                                                                              \n";
    $stSql .= "                                  FROM beneficio.grupo_concessao_vale_transporte AS BGCVT                                                                         \n";
    $stSql .= "                                  JOIN beneficio.grupo_concessao BGC                                                                                              \n";
    $stSql .= "                                    ON BGC.cod_grupo      = BGCVT.cod_grupo                                                                                       \n";
    $stSql .= "                                  JOIN beneficio.contrato_servidor_grupo_concessao_vale_transporte BCSGCVT                                                        \n";
    $stSql .= "                                    ON BGCVT.cod_grupo    = BCSGCVT.cod_grupo                                                                                     \n";
    $stSql .= "                                  JOIN pessoal.contrato PC                                                                                                        \n";
    $stSql .= "                                    ON PC.cod_contrato    = BCSGCVT.cod_contrato                                                                                  \n";
    $stSql .= "                                  JOIN pessoal.servidor_contrato_servidor PSCS                                                                                    \n";
    $stSql .= "                                    ON PSCS.cod_contrato  = BCSGCVT.cod_contrato                                                                                  \n";
    $stSql .= "                                  JOIN (  SELECT MAXPCSO.cod_contrato                                                                                             \n";
    $stSql .= "                                               , MAX(MAXPCSO.timestamp) as timestamp                                                                              \n";
    $stSql .= "                                            FROM pessoal.contrato_servidor_orgao MAXPCSO                                                                          \n";
    $stSql .= "                                        GROUP BY MAXPCSO.cod_contrato ) as MPCSO                                                                                  \n";
    $stSql .= "                                    ON MPCSO.cod_contrato = BCSGCVT.cod_contrato                                                                                  \n";
    $stSql .= "                                  JOIN pessoal.contrato_servidor_orgao PCSO                                                                                       \n";
    $stSql .= "                                    ON PCSO.cod_contrato  = MPCSO.cod_contrato                                                                                    \n";
    $stSql .= "                                   AND PCSO.timestamp     = MPCSO.timestamp                                                                                       \n";
    $stSql .= "                                  JOIN organograma.vw_orgao_nivel AS OO                                                                                           \n";
    $stSql .= "                                    ON OO.cod_orgao       = PCSO.cod_orgao                                                                                        \n";
    $stSql .= "                             LEFT JOIN (  SELECT MAXPCSL.cod_contrato                                                                                             \n";
    $stSql .= "                                               , MAX(MAXPCSL.timestamp) as timestamp                                                                              \n";
    $stSql .= "                                            FROM pessoal.contrato_servidor_local MAXPCSL                                                                          \n";
    $stSql .= "                                        GROUP BY MAXPCSL.cod_contrato ) as MPCSL                                                                                  \n";
    $stSql .= "                                    ON MPCSL.cod_contrato = BCSGCVT.cod_contrato                                                                                  \n";
    $stSql .= "                             LEFT JOIN pessoal.contrato_servidor_local PCSL                                                                                       \n";
    $stSql .= "                                    ON PCSL.cod_contrato  = MPCSL.cod_contrato                                                                                    \n";
    $stSql .= "                                   AND PCSL.timestamp     = MPCSL.timestamp                                                                                       \n";
    $stSql .= "                             LEFT JOIN organograma.\"local\" AS OL                                                                                                \n";
    $stSql .= "                                    ON OL.cod_local       = PCSL.cod_local                                                                                        \n";
    $stSql .= "                           ) AS PSCS                                                                                                                              \n";
    $stSql .= "                      JOIN pessoal.servidor PS                                                                                                                    \n";
    $stSql .= "                        ON PS.cod_servidor = PSCS.cod_servidor                                                                                                    \n";
    $stSql .= "                      JOIN sw_cgm CGM                                                                                                                             \n";
    $stSql .= "                        ON CGM.numcgm      = PS.numcgm                                                                                                            \n";
    $stSql .= "                   ) AS DADOS_CONTRATO                                                                                                                            \n";
    $stSql .= "                ON DADOS_CONTRATO.cod_concessao = BCVT.cod_concessao                                                                                              \n";
    $stSql .= "               AND DADOS_CONTRATO.exercicio     = BCVT.exercicio                                                                                                  \n";
    $stSql .= "               AND DADOS_CONTRATO.cod_mes       = BCVT.cod_mes                                                                                                    \n";
    $stSql .= "              JOIN beneficio.tipo_concessao_vale_transporte BTCVT                                                                                                 \n";
    $stSql .= "                ON BTCVT.cod_tipo            = BCVT.cod_tipo                                                                                                      \n";
    $stSql .= "              JOIN beneficio.vale_transporte BVT                                                                                                                  \n";
    $stSql .= "                ON BVT.cod_vale_transporte   = BCVT.cod_vale_transporte                                                                                           \n";
    $stSql .= "              JOIN sw_cgm CGM_FORNECEDOR                                                                                                                          \n";
    $stSql .= "                ON CGM_FORNECEDOR.numcgm     = BVT.fornecedor_vale_transporte_fornecedor_numcgm                                                                   \n";
    $stSql .= "              JOIN beneficio.itinerario BI                                                                                                                        \n";
    $stSql .= "                ON BI.vale_transporte_cod_vale_transporte = BVT.cod_vale_transporte                                                                               \n";
    $stSql .= "              JOIN beneficio.linha BL_ORIGEM                                                                                                                      \n";
    $stSql .= "                ON BL_ORIGEM.cod_linha       = BI.cod_linha_origem                                                                                                \n";
    $stSql .= "              JOIN beneficio.linha BL_DESTINO                                                                                                                     \n";
    $stSql .= "                ON BL_DESTINO.cod_linha      = BI.cod_linha_destino                                                                                               \n";
    $stSql .= "              JOIN sw_municipio MUN_ORIGEM                                                                                                                        \n";
    $stSql .= "                ON MUN_ORIGEM.cod_municipio  = BI.municipio_origem                                                                                                \n";
    $stSql .= "               AND MUN_ORIGEM.cod_uf         = BI.uf_origem                                                                                                       \n";
    $stSql .= "              JOIN sw_municipio MUN_DESTINO                                                                                                                       \n";
    $stSql .= "                ON MUN_DESTINO.cod_municipio = BI.municipio_destino                                                                                               \n";
    $stSql .= "               AND MUN_DESTINO.cod_uf        = BI.uf_destino                                                                                                      \n";
    $stSql .= "          ORDER BY BCVT.cod_concessao ) as tabela                                                                                                                 \n";
    $stSql .= "   WHERE tabela.numcgm > 0                                                                                                                                        \n";

    return $stSql;
}

}
