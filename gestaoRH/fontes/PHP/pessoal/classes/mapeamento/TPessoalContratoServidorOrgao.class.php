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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_ORGAO
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
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_ORGAO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorOrgao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPessoalContratoServidorOrgao()
    {
        parent::Persistente();
        $this->setTabela('pessoal.contrato_servidor_orgao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_contrato,cod_orgao,timestamp');

        $this->AddCampo('cod_contrato','integer',true,'',true,true);
        $this->AddCampo('cod_orgao','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,false);

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "SELECT contrato_servidor_orgao.*                                                                 \n";
        $stSql .= "     , contrato.registro                                                                         \n";
        $stSql .= "     , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao  \n";
        $stSql .= "     , orgao.orgao as cod_estrutural                                                             \n";
        $stSql .= "  FROM pessoal.contrato_servidor_orgao                                                           \n";
        $stSql .= "     , ( SELECT cod_contrato                                                                     \n";
        $stSql .= "              , max(timestamp) as timestamp                                                      \n";
        $stSql .= "           FROM pessoal.contrato_servidor_orgao                                                  \n";
        $stSql .= "       GROUP BY cod_contrato) as max_orgao                                                       \n";
        $stSql .= "     , pessoal.contrato_servidor                                                                 \n";
        $stSql .= "     , pessoal.contrato                                                                          \n";
        $stSql .= "     ,organograma.vw_orgao_nivel as orgao                                                        \n";
        $stSql .= " WHERE contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato                     \n";
        $stSql .= "   AND contrato.cod_contrato = contrato_servidor.cod_contrato                                    \n";
        $stSql .= "   AND contrato_servidor_orgao.cod_contrato = max_orgao.cod_contrato                             \n";
        $stSql .= "   AND contrato_servidor_orgao.timestamp    = max_orgao.timestamp                                \n";
        $stSql .= "   AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao                                    \n";

        return $stSql;
    }

    public function recuperaContratoServidorLotacaoComSubDivisaoAssentamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stFiltro .= "\n GROUP BY contrato_servidor_orgao.cod_contrato
                              , contrato_servidor_orgao.cod_orgao
                              , contrato_servidor_orgao.timestamp
                              , contrato.registro
                              , orgao.cod_orgao
                              , orgao.orgao ";

        return $this->executaRecupera("montaRecuperaContratoServidorLotacaoComSubDivisaoAssentamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContratoServidorLotacaoComSubDivisaoAssentamento()
    {
        $stSql  = "SELECT contrato_servidor_orgao.*
                         , contrato.registro
                         , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao
                         , orgao.orgao as cod_estrutural
                      FROM pessoal.contrato_servidor_orgao
                         , ( SELECT cod_contrato
                                  , max(timestamp) as timestamp
                               FROM pessoal.contrato_servidor_orgao
                           GROUP BY cod_contrato) as max_orgao
                         , pessoal.contrato_servidor
                         , pessoal.contrato
                         , organograma.vw_orgao_nivel as orgao
                         , pessoal.contrato_servidor_sub_divisao_funcao
                         , pessoal.assentamento_sub_divisao
                         , pessoal.contrato_servidor_situacao
                     WHERE contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
                       AND contrato.cod_contrato = contrato_servidor.cod_contrato
                       AND contrato_servidor_orgao.cod_contrato = max_orgao.cod_contrato
                       AND contrato_servidor_orgao.timestamp    = max_orgao.timestamp
                       AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao
                       AND contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_orgao.cod_contrato
                       AND assentamento_sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                       AND contrato_servidor_situacao.cod_contrato = contrato.cod_contrato
                       AND contrato_servidor_situacao.timestamp = (SELECT timestamp
                                                                   FROM pessoal.contrato_servidor_situacao
                                                                   WHERE cod_contrato = contrato_servidor.cod_contrato                                                               
                                                                   ORDER BY timestamp desc
                                                                   LIMIT 1)
                       AND contrato_servidor_situacao.situacao = 'A'
                ";

        return $stSql;
    }

    public function recuperaContratosFerias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContratosFerias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContratosFerias()
    {
        $stSql  = "SELECT contrato_servidor_orgao.*                                                                         \n";
        $stSql .= "     , servidor.numcgm                                                                                   \n";
        $stSql .= "  FROM pessoal.contrato_servidor_orgao                                                                   \n";
        $stSql .= "     , ( SELECT cod_contrato                                                                             \n";
        $stSql .= "              , max(timestamp) as timestamp                                                              \n";
        $stSql .= "           FROM pessoal.contrato_servidor_orgao                                                          \n";
        $stSql .= "       GROUP BY cod_contrato) as max_contrato_servidor_orgao                                             \n";
        $stSql .= "     , pessoal.contrato_servidor_regime_funcao                                                           \n";
        $stSql .= "     , (  SELECT cod_contrato                                                                            \n";
        $stSql .= "               , max(timestamp) as timestamp                                                             \n";
        $stSql .= "            FROM pessoal.contrato_servidor_regime_funcao                                                 \n";
        $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                    \n";
        $stSql .= "     , pessoal.servidor_contrato_servidor                                                                \n";
        $stSql .= "     , pessoal.servidor                                                                                  \n";
        $stSql .= " WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                   \n";
        $stSql .= "   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                         \n";
        $stSql .= "   AND contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato   \n";
        $stSql .= "   AND contrato_servidor_regime_funcao.timestamp    = max_contrato_servidor_regime_funcao.timestamp      \n";
        $stSql .= "   AND contrato_servidor_regime_funcao.cod_contrato = contrato_servidor_orgao.cod_contrato               \n";
        $stSql .= "   AND contrato_servidor_orgao.cod_contrato = servidor_contrato_servidor.cod_contrato                    \n";
        $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                   \n";
        $stSql .= "   AND NOT EXISTS (SELECT 1                                                                              \n";
        $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa                                           \n";
        $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato_servidor_orgao.cod_contrato   )    \n";

        return $stSql;
    }

    public function recuperaContratosDaLotacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContratosDaLotacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContratosDaLotacao()
    {
        $stSql  = "SELECT contrato_servidor_orgao.*                                                             \n";
        $stSql .= "     , (SELECT registro FROM pessoal.contrato where cod_contrato = contrato_servidor_orgao.cod_contrato) as registro  \n";
        $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                \n";
        $stSql .= "  FROM pessoal.contrato_servidor_orgao                                                       \n";
        $stSql .= "  JOIN pessoal.servidor_contrato_servidor                                                    \n";
        $stSql .= "    ON contrato_servidor_orgao.cod_contrato = servidor_contrato_servidor.cod_contrato        \n";
        $stSql .= "  JOIN pessoal.servidor                                                                      \n";
        $stSql .= "    ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                       \n";
        $stSql .= "  JOIN (  SELECT cod_contrato                                                                \n";
        $stSql .= "               , MAX(timestamp) as timestamp                                                 \n";
        $stSql .= "            FROM pessoal.contrato_servidor_orgao                                             \n";
        $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_orgao                                \n";
        $stSql .= "    ON contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato       \n";
        $stSql .= "   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp             \n";

        return $stSql;
    }

    public function recuperaOrganogramaVigente(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaOrganogramaVigente",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrganogramaVigente()
    {
        $stSql  = "  SELECT cod_organograma                                             \n";
        $stSql .= "    FROM organograma.orgao_nivel                                     \n";
        $stSql .= "   WHERE cod_orgao = (                                               \n";
        $stSql .= "                             SELECT cod_orgao                        \n";
        $stSql .= "                               FROM pessoal".Sessao::getEntidade().".contrato_servidor_orgao  \n";
        $stSql .= "                              WHERE timestamp <= ultimoTimestampPeriodoMovimentacao('".$this->getDado('cod_periodo_movimentacao')."', '".Sessao::getEntidade()."')::timestamp \n";
        $stSql .= "                           ORDER BY timestamp DESC                   \n";
        $stSql .= "                              LIMIT 1                                \n";
        $stSql .= "                      )                                              \n";
        $stSql .= "   LIMIT 1                                                           \n";

        return $stSql;
    }

    public function recuperaDataPrimeiroCadastro(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDataPrimeiroCadastro",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDataPrimeiroCadastro()
    {
        $stSql  = "  SELECT to_char(min(timestamp)::date,'dd/mm/yyyy') as dt_cadastro \n";
        $stSql .= "    FROM pessoal".Sessao::getEntidade().".contrato_servidor_orgao                           \n";

        return $stSql;
    }
}
