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
    * Classe de mapeamento da tabela ORCAMENTO.RESERVA_SALDOS
    * Efetua conexão com a tabela  ORCAMENTO.RESERVA_SALDOS
    * Data de Criação: 28/04/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.08
                    uc-02.01.28
                    uc-03.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoReservaSaldos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoReservaSaldos()
{
    parent::Persistente();
    $this->setTabela('orcamento.reserva_saldos');

    $this->setCampoCod('cod_reserva');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('cod_reserva','integer',true,'',true,false);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_despesa','integer',true,'',false,true);
    $this->AddCampo('dt_validade_inicial','date',true,'',false,false);
    $this->AddCampo('dt_validade_final','date',true,'',false,false);
    $this->AddCampo('dt_inclusao','date',true,'',false,false);
    $this->AddCampo('vl_reserva','numeric',true,'14,2',false,false);
    $this->AddCampo('tipo','char',false,'1',false,false);
    $this->AddCampo('motivo','text',true,'',false,false);
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
  public function montaRecuperaRelacionamento()
  {
    $stSql  = "";
    $stSql .= " SELECT                                                                                                  \n";
    $stSql .= "         publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '".$this->getDado('exercicio')."' ), dotacao ) as dotacao_formatada  \n";
    $stSql .= "         ,*                                                                                              \n";
    $stSql .= "     FROM                                                                                                \n";
    $stSql .= "     (                                                                                                   \n";
    $stSql .= " SELECT                                                                  \n";
    $stSql .= "     re.cod_reserva,                                                     \n";
    $stSql .= "     re.exercicio,                                                       \n";
    $stSql .= "     re.cod_despesa,                                                     \n";
    $stSql .= "     re.vl_reserva,                                                      \n";
    $stSql .= "     re.dt_inclusao,                                                     \n";
    $stSql .= "     TO_CHAR(re.dt_validade_inicial,'dd/mm/yyyy') as dt_validade_inicial,\n";
    $stSql .= "     TO_CHAR(re.dt_validade_final,'dd/mm/yyyy') as dt_validade_final,    \n";
    $stSql .= "     de.cod_entidade,                                                    \n";
    $stSql .= "     de.cod_recurso,                                                     \n";
    $stSql .= "     rec.masc_recurso_red,                                               \n";
    $stSql .= "     rec.cod_detalhamento,                                               \n";
    $stSql .= "     de.num_orgao,                                                       \n";
    $stSql .= "     de.num_unidade,                                                     \n";
    $stSql .= "     re.motivo,                                                          \n";
    $stSql .= "     re.vl_reserva,                                                      \n";
    $stSql .= "     ra.motivo_anulacao,                                                 \n";
    $stSql .= "     re.tipo as tipo,                                                    \n";
    $stSql .= "     TO_CHAR(ra.dt_anulacao,'dd/mm/yyyy') as dt_anulacao,                \n";

    /* ASSIM QUE UMA VERSÃO DO COMPRAS FOR ENVIADA PARA CLIENTES, O CÓDIGO ABAIXO DEVERÁ SER DESCOMENTADO */
    /*
    $stSql .= "	    CASE								    \n";
    $stSql .= "	       WHEN solicitacao_homologada_reserva.cod_reserva IS NOT NULL THEN 'S' \n";
    $stSql .= "	    ELSE								    \n";
    $stSql .= "	    CASE								    \n";
    $stSql .= "        WHEN mapa_item_reserva.cod_reserva IS NOT NULL THEN 'M'		    \n";
    $stSql .= "     ELSE								    \n";
    */
    $stSql .= "     CASE								    \n";
    $stSql .= "     WHEN autorizacao_reserva.cod_reserva IS NOT NULL THEN 'A' 		    \n";

    /* ASSIM QUE UMA VERSÃO DO COMPRAS FOR ENVIADA PARA CLIENTES, O CÓDIGO ABAIXO DEVERÁ SER DESCOMENTADO */
    /*
    $stSql .= "     END									    \n";
    $stSql .= "     END									    \n";
    */

    $stSql .= "		END AS origem,        						    \n";
    $stSql .= "     CASE                            	                                    \n";
    $stSql .= "       WHEN ra.dt_anulacao is not null THEN 'Anulada'	                    \n";
/* */
//  $stSql .= "       WHEN re.dt_validade_final < '".$this->getDado('dataAtual')."' THEN 'Inativa'           \n";
    $stSql .= "       WHEN re.dt_validade_final < TO_DATE('".$this->getDado('stDtInicial')."', 'dd/mm/yyyy') THEN 'Inativa' \n";
    $stSql .= "       WHEN re.dt_validade_inicial > TO_DATE('".$this->getDado('stDtFinal')."', 'dd/mm/yyyy') THEN 'Inativa' \n";

    /* ORIGINAL
    $stSql .= "       WHEN re.dt_validade_final < '".$this->getDado('dataAtual')."' THEN 'Inativa'           \n";
    $stSql .= "       WHEN re.dt_validade_inicial > '".$this->getDado('dataAtual')."' THEN 'Inativa'           \n";
*/
    $stSql .= "       ELSE 'Ativa'                                                      \n";
    $stSql .= "     END as situacao,                                                    \n";
    $stSql .= "     de.num_orgao                                                        \n";
    $stSql .= "     ||'.'||de.num_unidade                                               \n";
    $stSql .= "     ||'.'||de.cod_funcao                                                \n";
    $stSql .= "     ||'.'||de.cod_subfuncao                                             \n";
    $stSql .= "     ||'.'||programa.num_programa                                        \n";
    $stSql .= "     ||'.'||acao.num_acao                                                \n";
    $stSql .= "     ||'.'||replace(cd.cod_estrutural,'.','')                            \n";
    $stSql .= "     AS dotacao                                                          \n";
    $stSql .= "     FROM                                                                \n";
    $stSql .= "          orcamento.despesa           as de                              \n";
    $stSql .= "     JOIN orcamento.recurso(EXTRACT( YEAR FROM TO_DATE('".$this->getDado('stDtInicial')."', 'dd/mm/yyyy'))::varchar) as rec  \n";
    $stSql .= "       ON ( rec.cod_recurso = de.cod_recurso AND rec.exercicio = de.exercicio )  \n";
    $stSql .= "     JOIN orcamento.programa_ppa_programa                                        \n";
    $stSql .= "       ON programa_ppa_programa.cod_programa = de.cod_programa                   \n";
    $stSql .= "      AND programa_ppa_programa.exercicio   = de.exercicio                       \n";
    $stSql .= "     JOIN ppa.programa                                                           \n";
    $stSql .= "       ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa     \n";
    $stSql .= "     JOIN orcamento.pao_ppa_acao                                                 \n";
    $stSql .= "       ON pao_ppa_acao.num_pao = de.num_pao                                      \n";
    $stSql .= "      AND pao_ppa_acao.exercicio = de.exercicio                                  \n";
    $stSql .= "     JOIN ppa.acao                                                               \n";
    $stSql .= "       ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao,                             \n";
    $stSql .= "          orcamento.conta_despesa     as cd,                                     \n";
    $stSql .= "          orcamento.reserva_saldos    as re                                      \n";
    $stSql .= "     LEFT JOIN 									\n";
    $stSql .= "	         orcamento.reserva_saldos_anulada as ra 				\n";
    $stSql .= "       ON									\n";
    $stSql .= "	         ra.exercicio = re.exercicio                                     	\n";
    $stSql .= "	     AND ra.cod_reserva = re.cod_reserva					\n";

/* ASSIM QUE UMA VERSÃO DO COMPRAS FOR ENVIADA PARA CLIENTES, O CÓDIGO ABAIXO DEVERÁ SER DESCOMENTADO */
/*
    $stSql .= "     LEFT JOIN									\n";
    $stSql .= "	         compras.solicitacao_homologada_reserva					\n";
    $stSql .= "      ON										\n";
    $stSql .= "          re.cod_reserva   = solicitacao_homologada_reserva.cod_reserva		\n";
    $stSql .= "	    AND re.exercicio = solicitacao_homologada_reserva.exercicio			\n";
    $stSql .= "    LEFT JOIN									\n";
    $stSql .= "	        compras.mapa_item_reserva						\n";
    $stSql .= "      ON										\n";
    $stSql .= "	        re.cod_reserva   = mapa_item_reserva.cod_reserva			\n";
    $stSql .= "	    AND re.exercicio = mapa_item_reserva.exercicio_reserva			\n";
    */

    $stSql .= "    LEFT JOIN									\n";
    $stSql .= "	        empenho.autorizacao_reserva						\n";
    $stSql .= "      ON										\n";
    $stSql .= "	        re.cod_reserva   = autorizacao_reserva.cod_reserva			\n";
    $stSql .= "     AND re.exercicio = autorizacao_reserva.exercicio				\n";
    /* */
    $stSql .= "   WHERE                                                                         \n";
    $stSql .= "         re.exercicio    = de.exercicio                                          \n";
    $stSql .= "     AND re.cod_despesa  = de.cod_despesa                                        \n";
    $stSql .= "     AND de.exercicio    = cd.exercicio                                          \n";
    $stSql .= "     AND de.cod_conta    = cd.cod_conta                                          \n";
    $stSql .= ") as tabela                                                                      \n";
    $stSql .= "         ".$this->getDado('stFiltro')."                                          \n";

    return $stSql;
  }

function recuperaRelatorioNotaReserva(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelatorioNotaReserva().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioNotaReserva()
{
    $stSql .= "SELECT ORS.exercicio                                                             \n";
    $stSql .= "      ,ORS.cod_reserva                                                           \n";
    $stSql .= "      ,ORS.cod_despesa                                                           \n";
    $stSql .= "      ,OCD.descricao                                  AS nom_conta               \n";
    $stSql .= "      ,TO_CHAR( ORS.dt_inclusao      , 'dd/mm/yyyy' ) AS dt_inclusao             \n";
    $stSql .= "      ,TO_CHAR( ORS.dt_validade_final, 'dd/mm/yyyy' ) AS dt_validade_final       \n";
    $stSql .= "      ,ORS.vl_reserva                                                            \n";
    $stSql .= "      ,ORS.motivo                                                                \n";
    $stSql .= "      ,OD.cod_entidade                                                           \n";
    $stSql .= "      ,CGM.nom_cgm                                                               \n";
    $stSql .= "      ,OD.num_orgao                                                              \n";
    $stSql .= "      ,OO.nom_orgao                                                              \n";
    $stSql .= "      ,OD.num_unidade                                                            \n";
    $stSql .= "      ,OU.nom_unidade                                                            \n";
    $stSql .= "FROM orcamento.reserva_saldos AS ORS                                         \n";
    $stSql .= "    ,orcamento.despesa        AS OD                                          \n";
    $stSql .= "    ,orcamento.entidade       AS OE                                          \n";
    $stSql .= "    ,orcamento.conta_despesa  AS OCD                                         \n";
    $stSql .= "    ,sw_cgm                   AS CGM                                         \n";
    $stSql .= "    ,orcamento.orgao          AS OO                                          \n";
    $stSql .= "    ,orcamento.unidade        AS OU                                          \n";
    $stSql .= "  -- Join com despesa                                                            \n";
    $stSql .= "WHERE ORS.cod_despesa  = OD.cod_despesa                                          \n";
    $stSql .= "  AND ORS.exercicio    = OD.exercicio                                            \n";
    $stSql .= "  -- Join com entidade                                                           \n";
    $stSql .= "  AND OD.cod_entidade  = OE.cod_entidade                                         \n";
    $stSql .= "  AND OD.exercicio     = OE.exercicio                                            \n";
    $stSql .= "  -- Join com conta_despesa                                                      \n";
    $stSql .= "  AND OD.cod_conta     = OCD.cod_conta                                           \n";
    $stSql .= "  AND OD.exercicio     = OCD.exercicio                                           \n";
    $stSql .= "  -- Join com cgm                                                                \n";
    $stSql .= "  AND OE.numcgm        = CGM.numcgm                                              \n";
    $stSql .= "  -- Join com orgao                                                              \n";
    $stSql .= "  AND OD.num_orgao     = OO.num_orgao                                            \n";
    $stSql .= "  AND OD.exercicio     = OO.exercicio                                            \n";
    $stSql .= "  -- Join com unidade                                                            \n";
    $stSql .= "  AND OD.num_unidade   = OU.num_unidade                                          \n";
    $stSql .= "  AND OD.num_orgao     = OU.num_orgao                                            \n";
    $stSql .= "  AND OD.exercicio     = OU.exercicio                                            \n";

    return $stSql;
}

function incluiReservaSaldo()
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaincluiReservaSaldo ();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $rsRecordSet->getCampo ( 'fn_reserva_saldo' ) == 't';

}

function montaincluiReservaSaldo()
{
    $stSql  = "select orcamento.fn_reserva_saldo ( "           . " \n ";
    $stSql .= "  " . $this->getDado('cod_reserva'         )    . " \n ";
    $stSql .= " , '" . $this->getDado('exercicio'           )   . "' \n ";
    $stSql .= " , " . $this->getDado('cod_despesa'         )   . " \n ";
    $stSql .= " ,  TO_DATE('". $this->getDado('dt_validade_inicial' )   . "' ,'dd/mm/yyyy')  \n ";
    $stSql .= " ,  TO_DATE('". $this->getDado('dt_validade_final'   )   . "' ,'dd/mm/yyyy')  \n ";
    $stSql .= " , " . $this->getDado('vl_reserva'          )   . " \n ";
    $stSql .= " , '" . $this->getDado('tipo'                )   . "' \n ";
    $stSql .= " , '" . $this->getDado('motivo'              )   . "' \n ";
    $stSql .= " ) ; \n  ";

    return $stSql;

}

function alteraReservaSaldo()
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaAlteraReservaSaldo ();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $rsRecordSet->getCampo ( 'fn_altera_reserva_saldo' ) == 't';
}

function montaAlteraReservaSaldo()
{
    $stSql  = "select  orcamento.fn_altera_reserva_saldo ( " . $this->getDado('cod_reserva' ) . " , '" .
                                                               $this->getDado('exercicio' ). "' , " .
                                                               $this->getDado('vl_reserva') . " ) ";

    return $stSql;

}

function recuperaBloqueioOrcamentarioEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaBloqueioOrcamentarioEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaBloqueioOrcamentarioEsfinge()
{
    $stSql = "
                select reserva_saldos.cod_reserva
                    , licitacao.cod_licitacao
                    , despesa.num_unidade
                    , substr(despesa.num_pao::VARCHAR, 1, 1) as tipo_acao
                    , despesa.num_pao
                    , recurso.cod_fonte
                    , substr(conta_despesa.cod_estrutural, 1, 1) as categoria_economica
                    , substr(conta_despesa.cod_estrutural, 3, 1) as grupo_natureza_despesa
                    , substr(conta_despesa.cod_estrutural, 5, 1) || substr(conta_despesa.cod_estrutural, 7, 1) as modalidade_aplicacao
                    , substr(conta_despesa.cod_estrutural, 9, 2) as elemento
                    , to_char(reserva_saldos.dt_inclusao,'dd/mm/yyyy') as dt_inclusao
                    , reserva_saldos.vl_reserva
                from licitacao.licitacao

                join compras.mapa
                on mapa.exercicio = licitacao.exercicio_mapa
                and mapa.cod_mapa = licitacao.cod_mapa

                join compras.mapa_solicitacao
                on mapa_solicitacao.exercicio = mapa.exercicio
                and mapa_solicitacao.cod_mapa = mapa.cod_mapa

                join compras.solicitacao_homologada_reserva
                on solicitacao_homologada_reserva.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                and solicitacao_homologada_reserva.cod_entidade = mapa_solicitacao.cod_entidade
                and solicitacao_homologada_reserva.exercicio = mapa_solicitacao.exercicio_solicitacao

                join orcamento.reserva_saldos
                on reserva_saldos.cod_reserva = solicitacao_homologada_reserva.cod_reserva
                and reserva_saldos.exercicio = solicitacao_homologada_reserva.exercicio

                join orcamento.despesa
                on despesa.cod_despesa = reserva_saldos.cod_despesa
                and despesa.exercicio = reserva_saldos.exercicio

                join orcamento.conta_despesa
                on conta_despesa.exercicio = despesa.exercicio
                and conta_despesa.cod_conta = despesa.cod_conta

                join orcamento.recurso('".$this->getDado('exercicio')."') as recurso
                on recurso.exercicio = despesa.exercicio
                and recurso.cod_recurso = despesa.cod_recurso

                where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
                and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
                and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
                and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
    ";
    
    return $stSql;
}

}
