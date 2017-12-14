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
    * Classe de mapeamento da tabela folhaPagamento.concessao_decimo
    * Data de Criação: 14/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-11-09 12:11:07 -0200 (Sex, 09 Nov 2007) $

    * Casos de uso: uc-04.05.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhaPagamento.concessao_decimo
  * Data de Criação: 14/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConcessaoDecimo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConcessaoDecimo()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.concessao_decimo");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_periodo_movimentacao,cod_contrato,desdobramento');

    $this->AddCampo('cod_periodo_movimentacao','integer',false ,''      ,true,'TFolhaPagamentoPeriodoMovimentacao');
    $this->AddCampo('cod_contrato'            ,'integer',false ,''      ,true,'TPessoalContrato');
    $this->AddCampo('desdobramento'           ,'char'   ,false ,'1'     ,true,false);
    $this->AddCampo('folha_salario'           ,'boolean',false ,'FALSE' ,false,false);

}

function recuperaConcessoes(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaConcessoes().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConcessoes()
{
    $stSql.= "SELECT concessao_decimo.*                                              \n ";
    $stSql.= "     , contrato.registro                                               \n ";
    $stSql.= "     , sw_cgm.numcgm                                                   \n ";
    $stSql.= "     , sw_cgm.nom_cgm                                                  \n ";
    $stSql.= "  FROM folhapagamento.concessao_decimo                                 \n ";
    $stSql.= "     , pessoal.contrato                                                \n ";
    $stSql.= "     , pessoal.servidor_contrato_servidor                              \n ";
    $stSql.= "     , pessoal.servidor                                                \n ";
    $stSql.= "     , sw_cgm                                                          \n ";
    $stSql.= "WHERE concessao_decimo.cod_contrato = contrato.cod_contrato            \n ";
    $stSql.= "  AND contrato.cod_contrato = servidor_contrato_servidor.cod_contrato  \n ";
    $stSql.= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor  \n ";
    $stSql.= "  AND servidor.numcgm = sw_cgm.numcgm                                  \n ";

    return $stSql;
}

function recuperaContratosParaCancelarPensionista(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaContratosParaCancelarPensionista().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosParaCancelarPensionista()
{
    $stSql  = "    SELECT concessao_decimo.*                                                                                                  \n";
    $stSql .= "         , getDesdobramentoDecimo(desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto                            \n";
    $stSql .= "         , 'Confirma a exclusão da concessão do 13º Salário (Matrícula '||contrato_pensionista.registro||').' as mensagem      \n";
    $stSql .= "         , contrato_pensionista.registro                                                                                       \n";
    $stSql .= "         , contrato_pensionista.cod_contrato                                                                                   \n";
    $stSql .= "         , contrato_pensionista.numcgm                                                                                         \n";
    $stSql .= "         , contrato_pensionista.nom_cgm                                                                                        \n";
    $stSql .= "         , contrato_pensionista.desc_orgao                                                                                     \n";
    $stSql .= "         , contrato_pensionista.orgao as cod_estrutural                                                                        \n";
    $stSql .= "         , '' as desc_funcao                                                                                                   \n";
    $stSql .= "      FROM folhapagamento.concessao_decimo                                                                                     \n";
    $stSql .= "INNER JOIN ( SELECT *                                                                                                          \n";
    $stSql .= "               FROM recuperarContratoPensionista(                                                                              \n";
    $stSql .= "                                    '".$this->getDado("stConfiguracao")."',                                                    \n";
    $stSql .= "                                    '".Sessao::getEntidade()."',                                                               \n";
    $stSql .= "                                    0,                                                                                         \n";
    $stSql .= "                                    '".$this->getDado("stTipoFiltro")."',                                                      \n";
    $stSql .= "                                    '".$this->getDado("stValoresFiltro")."',                                                   \n";
    $stSql .= "                                    '".Sessao::getExercicio()."'                                                               \n";
    $stSql .= "                                    )) as contrato_pensionista                                                                 \n";
    $stSql .= "        ON concessao_decimo.cod_contrato = contrato_pensionista.cod_contrato                                                   \n";

    return $stSql;
}

function recuperaContratosParaCancelar(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaContratosParaCancelar().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosParaCancelar()
{
    $stSql  = "    SELECT concessao_decimo.*                                                                                          \n";
    $stSql .= "         , getDesdobramentoDecimo(desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto                    \n";
    $stSql .= "         , ( CASE WHEN evento_decimo_calculado.cod_contrato > 0                                                        \n";
    $stSql .= "                    THEN 'A Matrícula possui cálculo de 13º, ao confirmar, o cálculo será excluído.'                   \n";
    $stSql .= "                    ELSE 'Confirma a exclusão da concessão do 13º Salário (Matrícula '||contrato.registro||').'        \n";
    $stSql .= "             END ) as mensagem                                                                                         \n";
    $stSql .= "         , contrato.registro                                                                                           \n";
    $stSql .= "         , contrato.cod_contrato                                                                                       \n";
    $stSql .= "         , contrato.numcgm                                                                                             \n";
    $stSql .= "         , contrato.nom_cgm                                                                                            \n";
    $stSql .= "         , trim(contrato.desc_orgao) as desc_orgao                                                                     \n";
    $stSql .= "         , contrato.desc_funcao                                                                                        \n";
    $stSql .= "      FROM folhapagamento.concessao_decimo                                                                             \n";
    $stSql .= "INNER JOIN ( SELECT *                                                                                                  \n";
    $stSql .= "               FROM recuperarContratoServidor(                                                                         \n";
    $stSql .= "                                    '".$this->getDado("stConfiguracao")."',                                            \n";
    $stSql .= "                                    '".Sessao::getEntidade()."',                                                       \n";
    $stSql .= "                                    0,                                                                                 \n";
    $stSql .= "                                    '".$this->getDado("stTipoFiltro")."',                                              \n";
    $stSql .= "                                    '".$this->getDado("stValoresFiltro")."',                                           \n";
    $stSql .= "                                    '".Sessao::getExercicio()."'                                                       \n";
    $stSql .= "                                    )) as contrato                                                                     \n";
    $stSql .= "        ON concessao_decimo.cod_contrato = contrato.cod_contrato                                                       \n";
    $stSql .= " LEFT JOIN ( SELECT cod_contrato                                                                                       \n";
    $stSql .= "               FROM folhapagamento.evento_decimo_calculado                                                             \n";
    $stSql .= "                  , folhapagamento.registro_evento_decimo                                                              \n";
    $stSql .= "              WHERE evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                   \n";
    $stSql .= "                AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                  \n";
    $stSql .= "                AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                      \n";
    $stSql .= "                AND evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                     \n";
    $stSql .= "           GROUP BY cod_contrato) as evento_decimo_calculado                                                           \n";
    $stSql .= "        ON contrato.cod_contrato = evento_decimo_calculado.cod_contrato                                                \n";

    return $stSql;
}

function recuperarContratosPorLotacaoLocal(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato";
    $stSql = $this->montaRecuperarContratosPorLotacaoLocal().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperarContratosPorLotacaoLocal()
{
    $stSql .= "SELECT contrato_servidor_orgao.*                                                         \n";
    $stSql .= "     , contrato.registro                                                                 \n";
    $stSql .= "     , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao                                                                   \n";
    $stSql .= "     , vw_orgao_nivel.orgao as cod_estrutural                                            \n";
    $stSql .= "  FROM pessoal.contrato_servidor_orgao                                                   \n";
    $stSql.= "LEFT JOIN (SELECT contrato_servidor_local.cod_contrato                                                \n";
    $stSql.= "                , local.descricao as desc_local                                                       \n";
    $stSql.= "                , local.cod_local                                                                     \n";
    $stSql.= "             FROM pessoal.contrato_servidor_local                                                     \n";
    $stSql.= "                , (SELECT cod_contrato                                                                \n";
    $stSql.= "                        , max(timestamp) as timestamp                                                 \n";
    $stSql.= "                     FROM pessoal.contrato_servidor_local                                             \n";
    $stSql.= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                              \n";
    $stSql.= "                , organograma.local                                                                   \n";
    $stSql.= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato     \n";
    $stSql.= "              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp           \n";
    $stSql.= "              AND contrato_servidor_local.cod_local = local.cod_local) as contrato_servidor_local     \n";
    $stSql.= "       ON contrato_servidor_orgao.cod_contrato = contrato_servidor_local.cod_contrato                                \n";
    $stSql .= "     , ( SELECT cod_contrato                                                             \n";
    $stSql .= "              , max(timestamp) as timestamp                                              \n";
    $stSql .= "           FROM pessoal.contrato_servidor_orgao                                          \n";
    $stSql .= "       GROUP BY cod_contrato) as max_orgao                                               \n";
    $stSql .= "     , pessoal.contrato_servidor                                                         \n";
    $stSql .= "     , pessoal.contrato                                                                  \n";
    $stSql .= "     ,organograma.orgao                                                                  \n";
    $stSql .= "     ,organograma.orgao_nivel                                                            \n";
    $stSql .= "     ,organograma.nivel                                                                  \n";
    $stSql .= "     ,organograma.organograma                                                            \n";
    $stSql .= "     ,organograma.vw_orgao_nivel                                                         \n";
    $stSql .= " WHERE contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato             \n";
    $stSql .= "   AND contrato.cod_contrato = contrato_servidor.cod_contrato                            \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_contrato = max_orgao.cod_contrato                     \n";
    $stSql .= "   AND contrato_servidor_orgao.timestamp    = max_orgao.timestamp                        \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao                            \n";
    $stSql .= "   AND orgao.cod_orgao    = orgao_nivel.cod_orgao                                        \n";
    $stSql .= "   AND orgao_nivel.cod_organograma = nivel.cod_organograma                               \n";
    $stSql .= "   AND orgao_nivel.cod_nivel = nivel.cod_nivel                                           \n";
    $stSql .= "   AND nivel.cod_organograma = organograma.cod_organograma                               \n";
    $stSql .= "   AND orgao.cod_orgao    = vw_orgao_nivel.cod_orgao                                     \n";
    $stSql .= "   AND orgao.cod_norma    = vw_orgao_nivel.cod_norma                                     \n";
    $stSql .= "   AND nivel.cod_nivel    = vw_orgao_nivel.nivel                                         \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                              \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa                           \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato)  \n";

    return $stSql;
}

function recuperaContratosConcessaoDecimo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosConcessaoDecimo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosConcessaoDecimo()
{
    $stSql = "
            SELECT contrato.*
                 , servidor.numcgm
                 , sw_cgm.nom_cgm
              FROM pessoal.contrato
        INNER JOIN pessoal.servidor_contrato_servidor
                ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato
        INNER JOIN pessoal.servidor
                ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = servidor.numcgm

        INNER JOIN ultimo_contrato_servidor_orgao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_orgao
                ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_local('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_local
                ON contrato_servidor_local.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_regime_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_regime_funcao
                ON contrato_servidor_regime_funcao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_sub_divisao_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_sub_divisao_funcao
                ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_funcao
                ON contrato_servidor_funcao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_especialidade_funcao
                ON contrato_servidor_especialidade_funcao.cod_contrato = contrato.cod_contrato

             WHERE NOT EXISTS ( SELECT 1
                                  FROM pessoal.contrato_servidor_caso_causa
                                 WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato
                              )
    ";

    if ( $this->getDado("inCodOrgao") ) {
        $stSql .= " AND contrato_servidor_orgao.cod_orgao IN (".$this->getDado("inCodOrgao").")";
    }

    if ( $this->getDado("inCodLocal") ) {
        $stSql .= " AND contrato_servidor_local.cod_local IN ( ".$this->getDado("inCodLocal")." )";
    }

    if ( $this->getDado("inCodRegime") ) {
        $stSql .= " AND contrato_servidor_regime_funcao.cod_regime_funcao in ( ".$this->getDado("inCodRegime")." ) ";
    }

    if ( $this->getDado("inCodSubDivisao") ) {
        $stSql .= " AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao in ( ".$this->getDado("inCodSubDivisao")." ) ";
    }

    if ( $this->getDado("inCodCargo") ) {
        $stSql .= " AND contrato_servidor_funcao.cod_cargo in ( ".$this->getDado("inCodCargo")." ) ";
    }

    if ( $this->getDado("inCodEspecialidade") ) {
        $stSql .= " AND contrato_servidor_especialidade_funcao.cod_especialidade_funcao in ( ".$this->getDado("inCodEspecialidade")." ) ";
    }

    $stSql .= "
             UNION

            SELECT contrato.*
                 , pensionista.numcgm
                 , sw_cgm.nom_cgm
              FROM pessoal.contrato
        INNER JOIN pessoal.contrato_pensionista
                ON contrato_pensionista.cod_contrato = contrato.cod_contrato
        INNER JOIN pessoal.pensionista
                ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
               AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = pensionista.numcgm

        INNER JOIN ultimo_contrato_pensionista_orgao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_orgao
                ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_local('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_local
                ON contrato_servidor_local.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_regime_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_regime_funcao
                ON contrato_servidor_regime_funcao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_sub_divisao_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_sub_divisao_funcao
                ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_funcao
                ON contrato_servidor_funcao.cod_contrato = contrato.cod_contrato

         LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".Sessao::getEntidade()."', ".$this->getDado("inCodPeriodoMovimentacao")." ) as contrato_servidor_especialidade_funcao
                ON contrato_servidor_especialidade_funcao.cod_contrato = contrato.cod_contrato

             WHERE 1=1 ";

    if ( $this->getDado("inCodOrgao") ) {
        $stSql .= " AND contrato_servidor_orgao.cod_orgao IN (".$this->getDado("inCodOrgao").")";
    }

    if ( $this->getDado("inCodLocal") ) {
        $stSql .= " AND contrato_servidor_local.cod_local IN ( ".$this->getDado("inCodLocal")." )";
    }

    if ( $this->getDado("inCodRegime") ) {
        $stSql .= " AND contrato_servidor_regime_funcao.cod_regime_funcao in ( ".$this->getDado("inCodRegime")." ) ";
    }

    if ( $this->getDado("inCodSubDivisao") ) {
        $stSql .= " AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao in ( ".$this->getDado("inCodSubDivisao")." ) ";
    }

    if ( $this->getDado("inCodCargo") ) {
        $stSql .= " AND contrato_servidor_funcao.cod_cargo in ( ".$this->getDado("inCodCargo")." ) ";
    }

    if ( $this->getDado("inCodEspecialidade") ) {
        $stSql .= " AND contrato_servidor_especialidade_funcao.cod_especialidade_funcao in ( ".$this->getDado("inCodEspecialidade")." ) ";
    }

    return $stSql;
}

function recuperaContratosParaConcessaoDecimo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosParaConcessaoDecimo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosParaConcessaoDecimo()
{
    $stSql  = "SELECT contrato.*                                                                            \n";
    $stSql .= "  FROM recuperarContratoServidor( \n";
    $stSql .= "'".$this->getDado("stConfiguracao")."',\n";
    $stSql .= "'".Sessao::getEntidade()."',\n";
    $stSql .=     $this->getDado("inCodPeriodoMovimentacao").",\n";
    $stSql .= "'".$this->getDado("stTipoFiltro")."',\n";
    $stSql .= "'".$this->getDado("stValoresFiltro")."',\n";
    $stSql .= "'".Sessao::getExercicio()."'\n";
    $stSql .= ") as contrato  \n";
    $stSql .= " WHERE NOT EXISTS (SELECT 1                                                                  \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa     \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato) \n";

    return $stSql;
}

function recuperaContratosParaConcessaoDecimoFuncao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosParaConcessaoDecimoFuncao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosParaConcessaoDecimoFuncao()
{
    $stSql .= "SELECT contrato.*                                                                            \n";
    $stSql .= "  FROM pessoal.contrato                                            \n";
    $stSql .= "     , pessoal.contrato_servidor_funcao                            \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                \n";
    $stSql .= "               , max(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_funcao                  \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_funcao                               \n";
    $stSql .= " WHERE contrato.cod_contrato = contrato_servidor_funcao.cod_contrato                         \n";
    $stSql .= "   AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato     \n";
    $stSql .= "   AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp           \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                                  \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa     \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato) \n";

    return $stSql;
}


function recuperaContratosAdiantamentoDecidoMesAniversario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato";
    $stSql = $this->montaRecuperaContratosAdiantamentoDecidoMesAniversario().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosAdiantamentoDecidoMesAniversario()
{
    $stSql = "
            SELECT contrato.*
                 , servidor.numcgm
                 , sw_cgm.nom_cgm
                 , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'mm') as mes_nascimento
              FROM pessoal.contrato
        INNER JOIN pessoal.servidor_contrato_servidor
                ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato
        INNER JOIN pessoal.servidor
                ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = servidor.numcgm
        INNER JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
               AND TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'mm') = '".$this->getDado('mes_aniversario')."'
        INNER JOIN ultimo_contrato_servidor_orgao('".Sessao::getEntidade()."', ".$this->getDado("cod_periodo_movimentacao")." ) as contrato_servidor_orgao
                ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato

             WHERE NOT EXISTS ( SELECT 1
                                  FROM pessoal.contrato_servidor_caso_causa
                                 WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato
                              )
             
        UNION

            SELECT contrato.*
                 , pensionista.numcgm
                 , sw_cgm.nom_cgm
                 , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'mm') as mes_nascimento
              FROM pessoal.contrato
        INNER JOIN pessoal.contrato_pensionista
                ON contrato_pensionista.cod_contrato = contrato.cod_contrato
        INNER JOIN pessoal.pensionista
                ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
               AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = pensionista.numcgm
        INNER JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                AND TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'mm') = '".$this->getDado('mes_aniversario')."'
        INNER JOIN ultimo_contrato_pensionista_orgao('".Sessao::getEntidade()."', ".$this->getDado("cod_periodo_movimentacao")." ) as contrato_servidor_orgao
                ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato

         ";
    

    return $stSql;
}



}//END OF CLASS
