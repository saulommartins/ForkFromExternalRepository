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
    * Classe de mapeamento da tabela ALMOXARIFADO.NATUREZA_LANCAMENTO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TAlmoxarifadoNaturezaLancamento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.03.11
                    uc-03.03.17
                    uc-03.03.16
                    uc-03.03.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.NATUREZA_LANCAMENTO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoNaturezaLancamento extends Persistente
{

/**
    * Método Construtor
    * @access Private
*/
    public function TAlmoxarifadoNaturezaLancamento()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.natureza_lancamento');
        $this->setCampoCod('num_lancamento');
        $this->setComplementoChave('cod_natureza,tipo_natureza,exercicio_lancamento');

        $this->AddCampo('num_lancamento','sequence',true,'',true,false);
        $this->AddCampo('exercicio_lancamento','char',true,'4',true,false,false,Sessao::getExercicio());
        $this->AddCampo('tipo_natureza','char',true,'1',true,'TAlmoxarifadoNatureza');
        $this->AddCampo('cod_natureza','integer',true,'',true,'TAlmoxarifadoNatureza');
        $this->AddCampo('cgm_almoxarife','integer',true,'',false,'TAlmoxarifadoAlmoxarife','cgm_almoxarife',Sessao::read('numCgm'));
        $this->AddCampo('numcgm_usuario','integer',true,'',false,'TAdministracaoUsuario','numcgm');
        $this->AddCampo('timestamp','timestamp',false, '', false, false);
    }

    public function recuperaNaturezaLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaNaturezaLancamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaNaturezaLancamento()
    {

        $stSql .= "    select                                 \n";
        $stSql .= "        num_lancamento,                    \n";
        $stSql .= "        cod_natureza,                      \n";
        $stSql .= "        tipo_natureza,                     \n";
        $stSql .= "        exercicio_lancamento               \n";
        $stSql .= "    from                                   \n";
        $stSql .= "        almoxarifado.natureza_lancamento   \n";
        $stSql .= "    where                                  \n";
        $stSql .= "        cod_natureza = ".$this->getDado('cod_natureza')." and \n";
        $stSql .= "        tipo_natureza = '".$this->getDado('tipo_natureza')."' and \n";
        $stSql .= "        exercicio_lancamento = '".$this->getDado('exercicio_lancamento')."' and \n";
        $stSql .= "        num_lancamento = '".$this->getDado('num_lancamento')."'\n";

        return $stSql;

    }

    # Método que retorna o num_lancamento considerando o parâmetro 'numeracao_lancamento_estoque'.
    public function recuperaNumNaturezaLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoConfiguracao.class.php';
        $obTAlmoxarifadoConfiguracao = new TAlmoxarifadoConfiguracao;
        $obTAlmoxarifadoConfiguracao->setDado('parametro' ,'numeracao_lancamento_estoque');
        $obTAlmoxarifadoConfiguracao->setDado('exercicio' , Sessao::getExercicio());
        $obTAlmoxarifadoConfiguracao->recuperaPorChave($rsAlmoxarifadoConfiguracao);

        $stNumeracao = (trim($rsAlmoxarifadoConfiguracao->getCampo('valor')) == '' ? 'N' : $rsAlmoxarifadoConfiguracao->getCampo('valor'));

        if ($stNumeracao == 'N') {
            $stFiltro .= " AND cod_natureza  = ".$this->getDado('cod_natureza')."      \n";
            $stFiltro .= " AND tipo_natureza = '".$this->getDado('tipo_natureza')."'   \n";
        }

        $stSql = $this->montaRecuperaNumNaturezaLancamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaNumNaturezaLancamento()
    {
        $stSql .= "  SELECT  COALESCE(MAX(num_lancamento),0)+1 as num_lancamento                    \n";
        $stSql .= "                                                                               \n";
        $stSql .= "    FROM  almoxarifado.natureza_lancamento                                     \n";
        $stSql .= "                                                                               \n";
        $stSql .= "   WHERE  exercicio_lancamento = '".$this->getDado('exercicio_lancamento')."'  \n";
        $stSql .= "                                                                               \n";

        return $stSql;
    }

    public function recuperaDadosReemissao(&$rsRecordSet, $stFiltro = "", $stGrupo = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosReemissao().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDadosReemissao()
    {
        $stSql .= "     SELECT  TO_CHAR(natureza_lancamento.TIMESTAMP, 'dd/mm/yyyy') AS data                        \n";
        $stSql .= "          ,  natureza_lancamento.num_lancamento                                                  \n";
        $stSql .= "          ,  natureza.cod_natureza                                                               \n";
        $stSql .= "          ,  natureza.descricao                                                                  \n";
        $stSql .= "          ,  natureza_lancamento.exercicio_lancamento                                            \n";
        $stSql .= "          ,  lancamento_requisicao.exercicio                                                     \n";
        $stSql .= "       FROM  almoxarifado.natureza                                                               \n";
        $stSql .= "                                                                                                 \n";
        $stSql .= " INNER JOIN  almoxarifado.natureza_lancamento                                                    \n";
        $stSql .= "         ON  natureza_lancamento.cod_natureza   =  natureza.cod_natureza                         \n";
        $stSql .= "        AND  natureza_lancamento.tipo_natureza  =  natureza.tipo_natureza                        \n";
        $stSql .= "                                                                                                 \n";
        $stSql .= " INNER JOIN  almoxarifado.lancamento_material                                                    \n";
        $stSql .= "         ON  lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento \n";
        $stSql .= "        AND  lancamento_material.num_lancamento       = natureza_lancamento.num_lancamento       \n";
        $stSql .= "        AND  lancamento_material.cod_natureza         = natureza_lancamento.cod_natureza         \n";
        $stSql .= "        AND  lancamento_material.tipo_natureza        = natureza_lancamento.tipo_natureza        \n";
        $stSql .= "                                                                                                 \n";
        $stSql .= " LEFT JOIN  almoxarifado.lancamento_requisicao                                                  \n";
        $stSql .= "         ON  lancamento_requisicao.cod_lancamento   = lancamento_material.cod_lancamento         \n";
        $stSql .= "        AND  lancamento_requisicao.cod_item         = lancamento_material.cod_item               \n";
        $stSql .= "        AND  lancamento_requisicao.cod_centro       = lancamento_material.cod_centro             \n";
        $stSql .= "        AND  lancamento_requisicao.cod_marca        = lancamento_material.cod_marca              \n";
        $stSql .= "        AND  lancamento_requisicao.cod_almoxarifado = lancamento_material.cod_almoxarifado       \n";

        return $stSql;

    }

    public function recuperaDadosReemissaoSaidaRequisicao(&$rsRecordSet, $stFiltro = "", $stGrupo = "", $stOrdem = "",$boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosReemissaoSaidaRequisicao().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDadosReemissaoSaidaRequisicao()
    {
        $stSql .= " SELECT TO_CHAR(natureza_lancamento.TIMESTAMP, 'dd/mm/yyyy') as data                         \n";
        $stSql .= "      , natureza_lancamento.num_lancamento                                                   \n";
        $stSql .= "      , natureza.descricao                                                                   \n";
        $stSql .= "      , natureza_lancamento.exercicio_lancamento                                             \n";
        $stSql .= "      , lancamento_requisicao.cod_almoxarifado                                               \n";
        $stSql .= "      , lancamento_requisicao.cod_requisicao                                                 \n";
        $stSql .= "                                                                                             \n";
        $stSql .= "   FROM almoxarifado.lancamento_material                                                     \n";
        $stSql .= "                                                                                             \n";
        $stSql .= "   JOIN almoxarifado.natureza_lancamento                                                     \n";
        $stSql .= "     ON natureza_lancamento.exercicio_lancamento = lancamento_material.exercicio_lancamento  \n";
        $stSql .= "    AND natureza_lancamento.num_lancamento       = lancamento_material.num_lancamento        \n";
        $stSql .= "    AND natureza_lancamento.cod_natureza         = lancamento_material.cod_natureza          \n";
        $stSql .= "    AND natureza_lancamento.tipo_natureza        = lancamento_material.tipo_natureza         \n";
        $stSql .= "                                                                                             \n";
        $stSql .= "   JOIN almoxarifado.natureza                                                                \n";
        $stSql .= "     ON natureza.cod_natureza  = natureza_lancamento.cod_natureza                            \n";
        $stSql .= "    AND natureza.tipo_natureza = natureza_lancamento.tipo_natureza                           \n";
        $stSql .= "                                                                                             \n";
        $stSql .= "   JOIN almoxarifado.lancamento_requisicao                                                   \n";
        $stSql .= "     ON lancamento_requisicao.cod_almoxarifado = lancamento_material.cod_almoxarifado        \n";
        $stSql .= "    AND lancamento_requisicao.cod_marca        = lancamento_material.cod_marca               \n";
        $stSql .= "    AND lancamento_requisicao.cod_centro        = lancamento_material.cod_centro             \n";
        $stSql .= "    AND lancamento_requisicao.cod_item         = lancamento_material.cod_item                \n";
        $stSql .= "    AND lancamento_requisicao.cod_lancamento   = lancamento_material.cod_lancamento          \n";
        if ( $this->getDado('tipo_natureza') ) {
            $stSql .= " WHERE natureza_lancamento.tipo_natureza = '".$this->getDado('tipo_natureza')."'\n";
        }
        if ( $this->getDado('cod_natureza') ) {
            $stSql .= " AND natureza_lancamento.cod_natureza = ".$this->getDado('cod_natureza')."\n";
        }
        if ( $this->getDado('num_lancamento') ) {
            $stSql .= " AND natureza_lancamento.num_lancamento = ".$this->getDado('num_lancamento')."\n";
        }
        if ( $this->getDado('exercicio_lancamento') ) {
            $stSql .= " AND natureza_lancamento.exercicio_lancamento = '".$this->getDado('exercicio_lancamento')."'\n";
        }

        return $stSql;

    }

    public function recuperaDadosReemissaoSaidaEstornoEntrada(&$rsRecordSet, $stFiltro = "", $stGrupo = "", $stOrdem = "",$boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosReemissaoSaidaEstornoEntrada().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDadosReemissaoSaidaEstornoEntrada()
    {
        $stSql .= " SELECT num_lancamento                                                                                                      \n";
        $stSql .= "      , cod_lancamento                                                                                                      \n";
        $stSql .= "   FROM almoxarifado.lancamento_material                                                                                    \n";
        $stSql .= "  WHERE cod_lancamento = ( SELECT lancamento_material_estorno.cod_lancamento                                                \n";
        $stSql .= "                             FROM almoxarifado.natureza_lancamento                                                          \n";
        $stSql .= "                             JOIN almoxarifado.lancamento_material                                                          \n";
        $stSql .= "                               ON lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento       \n";
        $stSql .= "                              AND lancamento_material.num_lancamento       = natureza_lancamento.num_lancamento             \n";
        $stSql .= "                              AND lancamento_material.cod_natureza         = natureza_lancamento.cod_natureza               \n";
        $stSql .= "                              AND lancamento_material.tipo_natureza        = natureza_lancamento.tipo_natureza              \n";
        $stSql .= "                             JOIN almoxarifado.lancamento_material_estorno                                                  \n";
        $stSql .= "                               ON lancamento_material_estorno.cod_lancamento_estorno = lancamento_material.cod_lancamento   \n";
        $stSql .= "                              AND lancamento_material_estorno.cod_almoxarifado       = lancamento_material.cod_almoxarifado \n";
        $stSql .= "                              AND lancamento_material_estorno.cod_item               = lancamento_material.cod_item         \n";
        $stSql .= "                              AND lancamento_material_estorno.cod_marca              = lancamento_material.cod_marca        \n";
        $stSql .= "                              AND lancamento_material_estorno.cod_centro             = lancamento_material.cod_centro       \n";
        if ( $this->getDado('tipo_natureza') ) {
        $stSql .= "                            WHERE natureza_lancamento.tipo_natureza = '".$this->getDado('tipo_natureza')."'                 \n";
        }
        if ( $this->getDado('cod_natureza') ) {
        $stSql .= "                              AND natureza_lancamento.cod_natureza = ".$this->getDado('cod_natureza')."                     \n";
        }
        if ( $this->getDado('num_lancamento') ) {
        $stSql .= "                              AND natureza_lancamento.num_lancamento = ".$this->getDado('num_lancamento')."                 \n";
        }
        if ( $this->getDado('exercicio_lancamento') ) {
        $stSql .= "                              AND natureza_lancamento.exercicio_lancamento = '".$this->getDado('exercicio_lancamento')."'   \n";
        $stSql .= "                            limit 1 )  \n";
        }

        return $stSql;
    }

    public function recuperaTotalPagina(&$rsRecordSet, $stFiltro = "", $stGrupo = "", $stOrdem = "",$boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotalPagina().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaTotalPagina()
    {
        $stSql .= " SELECT trunc(((count (lancamento_material.cod_item )::numeric(14,2))/16)::numeric(14,2)+1.5) as total_pagina \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "   FROM almoxarifado.natureza_lancamento                                                                      \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "   JOIN almoxarifado.lancamento_material                                                                      \n";
        $stSql .= "     ON lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento                   \n";
        $stSql .= "    AND lancamento_material.num_lancamento       = natureza_lancamento.num_lancamento                         \n";
        $stSql .= "    AND lancamento_material.cod_natureza         = natureza_lancamento.cod_natureza                           \n";
        $stSql .= "    AND lancamento_material.tipo_natureza        = natureza_lancamento.tipo_natureza                          \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "   JOIN almoxarifado.transferencia_almoxarifado_item_destino                                                  \n";
        $stSql .= "     ON transferencia_almoxarifado_item_destino.cod_item           = lancamento_material.cod_item             \n";
        $stSql .= "    AND transferencia_almoxarifado_item_destino.cod_marca          = lancamento_material.cod_marca            \n";
        $stSql .= "    AND transferencia_almoxarifado_item_destino.cod_centro_destino = lancamento_material.cod_centro           \n";
        $stSql .= "    AND transferencia_almoxarifado_item_destino.cod_lancamento     = lancamento_material.cod_lancamento       \n";
        $stSql .= "    AND transferencia_almoxarifado_item_destino.cod_almoxarifado   = lancamento_material.cod_almoxarifado     \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "  WHERE natureza_lancamento.tipo_natureza        = '".$this->getDado('tipo_natureza')."'                      \n";
        $stSql .= "    AND natureza_lancamento.cod_natureza         = ".$this->getDado('cod_natureza')."                         \n";
        $stSql .= "    AND natureza_lancamento.num_lancamento       = ".$this->getDado('num_lancamento')."                       \n";
        $stSql .= "    AND natureza_lancamento.exercicio_lancamento = '".$this->getDado('exercicio_lancamento')."'               \n";

        return $stSql;
    }

    public function recuperaTotalPaginaSaida(&$rsRecordSet, $stFiltro = "", $stGrupo = "", $stOrdem = "",$boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotalPaginaSaida().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaTotalPaginaSaida()
    {
        $stSql .= " SELECT trunc(((count (lancamento_material.cod_item )::numeric(14,2))/16)::numeric(14,2)+1.5) as total_pagina \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "   FROM almoxarifado.natureza_lancamento                                                                      \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "   JOIN almoxarifado.lancamento_material                                                                      \n";
        $stSql .= "     ON lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento                   \n";
        $stSql .= "    AND lancamento_material.num_lancamento       = natureza_lancamento.num_lancamento                         \n";
        $stSql .= "    AND lancamento_material.cod_natureza         = natureza_lancamento.cod_natureza                           \n";
        $stSql .= "    AND lancamento_material.tipo_natureza        = natureza_lancamento.tipo_natureza                          \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "   JOIN almoxarifado.transferencia_almoxarifado_item                                                  \n";
        $stSql .= "     ON transferencia_almoxarifado_item.cod_item           = lancamento_material.cod_item             \n";
        $stSql .= "    AND transferencia_almoxarifado_item.cod_marca          = lancamento_material.cod_marca            \n";
        $stSql .= "    AND transferencia_almoxarifado_item.cod_centro         = lancamento_material.cod_centro           \n";
        $stSql .= "    AND transferencia_almoxarifado_item.cod_lancamento     = lancamento_material.cod_lancamento       \n";
        $stSql .= "    AND transferencia_almoxarifado_item.cod_almoxarifado   = lancamento_material.cod_almoxarifado     \n";
        $stSql .= "                                                                                                              \n";
        $stSql .= "  WHERE natureza_lancamento.tipo_natureza        = '".$this->getDado('tipo_natureza')."'                      \n";
        $stSql .= "    AND natureza_lancamento.cod_natureza         = ".$this->getDado('cod_natureza')."                         \n";
        $stSql .= "    AND natureza_lancamento.num_lancamento       = ".$this->getDado('num_lancamento')."                       \n";
        $stSql .= "    AND natureza_lancamento.exercicio_lancamento = '".$this->getDado('exercicio_lancamento')."'               \n";

        return $stSql;
    }

}
