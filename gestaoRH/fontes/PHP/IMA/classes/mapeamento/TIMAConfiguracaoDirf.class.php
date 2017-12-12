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
    * Classe de mapeamento da tabela ima.configuracao_dirf
    * Data de Criacão: 21/11/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.14

  * @package URBEM
  * @subpackage Mapeamento

    $Id: TIMAConfiguracaoDirf.class.php 66267 2016-08-04 14:33:27Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TIMAConfiguracaoDirf extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConfiguracaoDirf()
{
    parent::Persistente();
    $this->setTabela("ima.configuracao_dirf");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio'             ,'char'   ,true  ,'4'  ,true,false);
    $this->AddCampo('cod_natureza'          ,'integer',true  ,''   ,false,'TIMANaturezaEstabelecimento');
    $this->AddCampo('responsavel_prefeitura','integer',true  ,''   ,false,'TCGMCGM','numcgm');
    $this->AddCampo('responsavel_entrega'   ,'integer',true  ,''   ,false,false);
    $this->AddCampo('telefone'              ,'varchar',true  ,'11' ,false,false);
    $this->AddCampo('ramal'                 ,'varchar',true  ,'5'  ,false,false);
    $this->AddCampo('fax'                   ,'varchar',true  ,'11' ,false,false);
    $this->AddCampo('email'                 ,'varchar',true  ,'30' ,false,false);
    $this->AddCampo('pagamento_mes_competencia','boolean', true, '', false, false);
    $this->AddCampo('cod_evento_molestia'   ,'integer', false, '', true, false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT configuracao_dirf.*                                                                                                               \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE sw_cgm.numcgm = configuracao_dirf.responsavel_prefeitura) as responsavel_prefeitura_nome        \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE sw_cgm.numcgm = configuracao_dirf.responsavel_entrega) as responsavel_entrega_nome              \n";
    $stSql .= "     , natureza_estabelecimento.descricao                                                                                                \n";
    $stSql .= "  FROM ima.configuracao_dirf                                                                                   \n";
    $stSql .= "     , ima.natureza_estabelecimento                                                                            \n";
    $stSql .= " WHERE configuracao_dirf.cod_natureza = natureza_estabelecimento.cod_natureza                                                            \n";

    return $stSql;
}

function recuperaExportarDirf(&$rsRecordSet, $boTransacao="", $stCondicao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaExportarDirf().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
    }

function montaRecuperaExportarDirf()
{
    $stSql  = "    SELECT uso_declarante                 \n";
    $stSql .= "         , sequencia                      \n";
    $stSql .= "         , nome_beneficiario              \n";
    $stSql .= "         , beneficiario                   \n";
    $stSql .= "         , ident_especializacao           \n";
    $stSql .= "         , codigo_retencao                \n";
    $stSql .= "         , ident_especie_beneficiario     \n";
    $stSql .= "         , jan                            \n";
    $stSql .= "         , fev                            \n";
    $stSql .= "         , mar                            \n";
    $stSql .= "         , abr                            \n";
    $stSql .= "         , mai                            \n";
    $stSql .= "         , jun                            \n";
    $stSql .= "         , jul                            \n";
    $stSql .= "         , ago                            \n";
    $stSql .= "         , set                            \n";
    $stSql .= "         , out                            \n";
    $stSql .= "         , nov                            \n";
    $stSql .= "         , dez                            \n";
    $stSql .= "         , dec                            \n";
    $stSql .= "      FROM dirf_reduzida('".Sessao::getEntidade()."',".$this->getDado("inExercicio").",'".$this->getDado("stTipoFiltro")."','".$this->getDado("stCodigos")."') \n";
    $stSql .= "  ORDER BY codigo_retencao, ident_especie_beneficiario \n";

    return $stSql;
}

function recuperaExportarDirfPrestadorServico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaExportarDirfPrestadorServico($boTransacao).$stFiltro.$stOrder;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
    
function montaRecuperaExportarDirfPrestadorServico(&$boTransacao)
{
    $stSql .= "    SELECT uso_declarante                 \n";
    $stSql .= "         , sequencia                      \n";
    $stSql .= "         , remove_acentos(nome_beneficiario) as nome_beneficiario              \n";
    $stSql .= "         , beneficiario                   \n";
    $stSql .= "         , ident_especializacao           \n";
    $stSql .= "         , codigo_retencao                \n";
    $stSql .= "         , ident_especie_beneficiario     \n";
    $stSql .= "         ,CASE WHEN ident_especializacao = '0' THEN        \n";
    $stSql .= "             (                                             \n";
    $stSql .= "                 SELECT dez                                \n";
    $stSql .= "                   FROM dirf_prestadores_servico_reduzida( \n";
    $stSql .= "                         '".Sessao::getEntidade()."'       \n";
    $stSql .= "                        ,'".Sessao::getCodEntidade($boTransacao)."'    \n";
    $stSql .= "                        ,".$this->getDado('inExercicioAnterior')." \n";
    $stSql .= "                        )                                  \n";
    $stSql .= "                  WHERE ident_especializacao = '0'         \n";
    $stSql .= "               ORDER BY  codigo_retencao                   \n";
    $stSql .= "                        ,ident_especie_beneficiario        \n";
    $stSql .= "              )                                            \n";
    $stSql .= "              WHEN ident_especializacao = '1' THEN         \n";
    $stSql .= "              (                                            \n";
    $stSql .= "                  SELECT dez                               \n";
    $stSql .= "                   FROM dirf_prestadores_servico_reduzida( \n";
    $stSql .= "                         '".Sessao::getEntidade()."'       \n";
    $stSql .= "                        ,'".Sessao::getCodEntidade($boTransacao)."'    \n";
    $stSql .= "                        ,".$this->getDado('inExercicioAnterior')."\n";
    $stSql .= "                        )                                  \n";
    $stSql .= "                  WHERE ident_especializacao = '0'         \n";
    $stSql .= "               ORDER BY  codigo_retencao                   \n";
    $stSql .= "                        ,ident_especie_beneficiario        \n";
    $stSql .= "              )                                            \n";
    $stSql .= "         END AS jan                                        \n";
    $stSql .= "         , jan AS fev                    \n";
    $stSql .= "         , fev AS mar                    \n";
    $stSql .= "         , mar AS abr                    \n";
    $stSql .= "         , abr AS mai                    \n";
    $stSql .= "         , mai AS jun                    \n";
    $stSql .= "         , jun AS jul                    \n";
    $stSql .= "         , jul AS ago                    \n";
    $stSql .= "         , ago AS set                    \n";
    $stSql .= "         , set AS out                    \n";
    $stSql .= "         , out AS nov                    \n";
    $stSql .= "         , nov AS dez                     \n";
    $stSql .= "         , dec                         \n";
    $stSql .= "     FROM dirf_prestadores_servico_reduzida('".Sessao::getEntidade()."','".Sessao::getCodEntidade($boTransacao)."',".$this->getDado("inExercicio").") \n";
    $stSql .= " ORDER BY codigo_retencao, ident_especie_beneficiario \n";

    return $stSql;
}

function recuperaExportarDirfPrestadorServicoPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaExportarDirfPrestadorServicoPagamento($boTransacao).$stFiltro.$stOrder;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaExportarDirfPrestadorServicoPagamento(&$boTransacao)
{
    $stSql  = "    SELECT uso_declarante                 \n";
    $stSql .= "         , sequencia                      \n";
    $stSql .= "         , nome_beneficiario              \n";
    $stSql .= "         , beneficiario                   \n";
    $stSql .= "         , ident_especializacao           \n";
    $stSql .= "         , codigo_retencao                \n";
    $stSql .= "         , ident_especie_beneficiario     \n";
    $stSql .= "         , jan                            \n";
    $stSql .= "         , fev                            \n";
    $stSql .= "         , mar                            \n";
    $stSql .= "         , abr                            \n";
    $stSql .= "         , mai                            \n";
    $stSql .= "         , jun                            \n";
    $stSql .= "         , jul                            \n";
    $stSql .= "         , ago                            \n";
    $stSql .= "         , set                            \n";
    $stSql .= "         , out                            \n";
    $stSql .= "         , nov                            \n";
    $stSql .= "         , dez                            \n";
    $stSql .= "         , dec                            \n";
    $stSql .= "     FROM dirf_prestadores_servico_reduzida('".Sessao::getEntidade()."','".Sessao::getCodEntidade($boTransacao)."',".$this->getDado("inExercicio").") \n";
    $stSql .= " ORDER BY codigo_retencao, ident_especie_beneficiario \n";

    return $stSql;
}

function recuperaExportarDirfPrestadorServicoPagamentoComESemRetencao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{    
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaExportarDirfPrestadorServicoPagamentoComESemRetencao($boTransacao).$stFiltro.$stOrder;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaExportarDirfPrestadorServicoPagamentoComESemRetencao(&$boTransacao)
{
    $stSql  = "    SELECT uso_declarante                 \n";
    $stSql .= "         , sequencia                      \n";
    $stSql .= "         , nome_beneficiario              \n";
    $stSql .= "         , beneficiario                   \n";
    $stSql .= "         , ident_especializacao           \n";
    $stSql .= "         , codigo_retencao                \n";
    $stSql .= "         , ident_especie_beneficiario     \n";
    $stSql .= "         , jan                            \n";
    $stSql .= "         , fev                            \n";
    $stSql .= "         , mar                            \n";
    $stSql .= "         , abr                            \n";
    $stSql .= "         , mai                            \n";
    $stSql .= "         , jun                            \n";
    $stSql .= "         , jul                            \n";
    $stSql .= "         , ago                            \n";
    $stSql .= "         , set                            \n";
    $stSql .= "         , out                            \n";
    $stSql .= "         , nov                            \n";
    $stSql .= "         , dez                            \n";
    $stSql .= "         , dec                            \n";
    $stSql .= "     FROM dirf_prestadores_servico_reduzida_pagamentos('".Sessao::getEntidade()."','".Sessao::getCodEntidade($boTransacao)."',".$this->getDado("inExercicio").") \n";
    $stSql .= " ORDER BY codigo_retencao, ident_especie_beneficiario \n";

    return $stSql;
}

function recuperaExportarDirfPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaExportarDirfPagamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaExportarDirfPagamento()
{
    $stSql .= "    SELECT  uso_declarante                                               \n";
    $stSql .= "           ,sequencia                                                    \n";
    $stSql .= "           ,nome_beneficiario                                            \n";
    $stSql .= "           ,beneficiario                                                 \n";
    $stSql .= "           ,ident_especializacao                                         \n";
    $stSql .= "           ,codigo_retencao                                              \n";
    $stSql .= "           ,ident_especie_beneficiario                                   \n";
    $stSql .= "           ,CASE WHEN ident_especializacao = '0' THEN                    \n";
    $stSql .= "                (                                                        \n";
    $stSql .= "                    SELECT dez                                           \n";
    $stSql .= "                      FROM dirf_reduzida(                                \n";
    $stSql .= "                            '".Sessao::getEntidade()."'                  \n";
    $stSql .= "                           ,".($this->getDado('inExercicio') - 1)."      \n";
    $stSql .= "                           ,'contrato_todos'                             \n";
    $stSql .= "                           ,(SELECT cod_contrato::VARCHAR FROM pessoal.contrato WHERE registro = uso_declarante)\n";
    $stSql .= "                           )                                             \n";
    $stSql .= "                     WHERE ident_especializacao = '0'                    \n";
    $stSql .= "                    ORDER BY codigo_retencao                             \n";
    $stSql .= "                            ,ident_especie_beneficiario                  \n";
    $stSql .= "                )                                                        \n";
    $stSql .= "                WHEN ident_especializacao = '1' THEN                     \n";
    $stSql .= "                (                                                        \n";
    $stSql .= "                    SELECT dez                                           \n";
    $stSql .= "                      FROM dirf_reduzida(                                \n";
    $stSql .= "                            '".Sessao::getEntidade()."'                  \n";
    $stSql .= "                           ,".($this->getDado('inExercicio')-1)."        \n";
    $stSql .= "                           ,'contrato_todos'                             \n";
    $stSql .= "                           ,(SELECT cod_contrato::VARCHAR FROM pessoal.contrato WHERE registro = uso_declarante)\n";
    $stSql .= "                           )                                             \n";
    $stSql .= "                     WHERE ident_especializacao = '1'                    \n";
    $stSql .= "                    ORDER BY codigo_retencao                             \n";
    $stSql .= "                            ,ident_especie_beneficiario                  \n";
    $stSql .= "                )                                                        \n";
    $stSql .= "           END AS jan                                                    \n";
    $stSql .= "           ,jan as fev                                                   \n";
    $stSql .= "           ,fev as mar                                                   \n";
    $stSql .= "           ,mar as abr                                                   \n";
    $stSql .= "           ,abr as mai                                                   \n";
    $stSql .= "           ,mai as jun                                                   \n";
    $stSql .= "           ,jun as jul                                                   \n";
    $stSql .= "           ,jul as ago                                                   \n";
    $stSql .= "           ,ago as set                                                   \n";
    $stSql .= "           ,set as out                                                   \n";
    $stSql .= "           ,out as nov                                                   \n";
    $stSql .= "           ,nov as dez                                                   \n";
    $stSql .= "           ,dec                                                          \n";
    $stSql .= "      FROM dirf_reduzida(                                                \n";
    $stSql .= "            '".Sessao::getEntidade()."'                                  \n";
    $stSql .= "           ,".$this->getDado('inExercicio')."                            \n";
    $stSql .= "           ,'".$this->getDado('stTipoFiltro')."'                         \n";
    $stSql .= "           ,'".$this->getDado('stCodigos')."'                            \n";
    $stSql .= "           )                                                             \n";
    $stSql .= "                                                                         \n";
    $stSql .= "    ORDER BY codigo_retencao                                             \n";
    $stSql .= "            ,ident_especie_beneficiario;                                 \n";

    return $stSql;
}

function recuperaDadosIRRF(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaDadosIRRF().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosIRRF()
{
    $stSql = "  SELECT 
                            plano_analitica.cod_plano as cod_receita_irrf
                            ,plano_conta.cod_estrutural
                            ,plano_conta.nom_conta as descricao                                          
                    FROM ima.configuracao_dirf_irrf_plano_conta                                
                    
                    INNER JOIN contabilidade.plano_conta                                 
                         ON configuracao_dirf_irrf_plano_conta.cod_conta = plano_conta.cod_conta  
                        AND configuracao_dirf_irrf_plano_conta.exercicio = plano_conta.exercicio  
                    
                    INNER JOIN contabilidade.plano_analitica
                         ON plano_analitica.exercicio = plano_conta.exercicio
                        AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                    WHERE configuracao_dirf_irrf_plano_conta.exercicio = '".$this->getDado('exercicio')."'

                    UNION
                    
                    SELECT 
                            receita.cod_receita as cod_receita_irrf
                            ,conta_receita.cod_estrutural
                            ,conta_receita.descricao
                    FROM orcamento.receita 
                    
                    INNER JOIN orcamento.conta_receita
                         ON conta_receita.cod_conta  = receita.cod_conta
                        AND conta_receita.exercicio = receita.exercicio
                    
                    INNER JOIN ima.configuracao_dirf_irrf_conta_receita
                         ON ima.configuracao_dirf_irrf_conta_receita.cod_conta      = conta_receita.cod_conta
                        AND ima.configuracao_dirf_irrf_conta_receita.exercicio     = conta_receita.exercicio

                    WHERE conta_receita.exercicio = '".$this->getDado('exercicio')."'
                ";

        return $stSql;

}

}
?>
