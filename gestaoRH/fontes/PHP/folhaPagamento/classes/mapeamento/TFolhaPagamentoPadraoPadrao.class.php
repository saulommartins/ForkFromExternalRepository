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
* Classe de regra de negócio para Pessoal-PadraoPadrao
* Data de Criação: 04/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage  Mapeamento

$Revision: 31001 $
$Name$
$Author: souzadl $
$Date: 2007-12-06 09:52:53 -0200 (Qui, 06 Dez 2007) $

* Casos de uso: uc-04.05.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.PADRAOPADRAO
  * Data de Criação: 04/10/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoPadraoPadrao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TFolhaPagamentoPadraoPadrao()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.padrao_padrao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_padrao,timestamp');

        $this->AddCampo('cod_padrao'    , 'integer'      , true,    ''    ,  true, true);
        $this->AddCampo('valor'         , 'numeric'      , true,    '14,2',  false, false);
        $this->AddCampo('timestamp'     , 'timestamp_now', true,    ''    ,  true, false);
        $this->AddCampo('cod_norma'     , 'integer'      , true,    ''    , false, true);
        $this->AddCampo('vigencia'      , 'date'         , true,    ''    , false, false);
    }

    public function recuperaAjustesSalariais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaAjustesSalariais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaAjustesSalariais()
    {
        if ($this->getDado("stSituacao") == "E") {
            $stSql  = "   SELECT contrato.cod_contrato\n";
            $stSql .= "        , contrato.registro\n";
            $stSql .= "        , contrato.nom_cgm\n";
            $stSql .= "        , contrato.desc_orgao as orgao\n";
            $stSql .= "        , contrato.desc_regime_funcao as regime\n";
            $stSql .= "        , contrato.desc_sub_divisao_funcao as sub_divisao\n";
            $stSql .= "        , contrato.desc_funcao as funcao\n";
            $stSql .= "        , contrato.desc_especialidade_funcao as especialidade\n";
            if ($this->getDado('stTipoReajuste') == 'v') {/* valor absoluto */
                if ($this->getDado("stFixado") == "V") {
                    $stSql .= "        , to_real(valor_registro) AS valor\n";
                    $stSql .= "        , to_real(".$this->getDado("nuValorNovo")."::numeric(14,2)) AS valor_novo\n";
                } else {
                    $stSql .= "        , to_real(quantidade_registro) AS valor\n";
                    $stSql .= "        , to_real(".$this->getDado("nuValorNovo")."::numeric(14,2)) AS valor_novo\n";
                }
            } else {/* reajuste por percentual */
                if ($this->getDado("stFixado") == "V") {
                    $stSql .= "        , to_real(valor_registro) AS valor                                                                                                                                                      \n";     //
                    $stSql .= "        , to_real(valor_registro+(valor_registro*".$this->getDado("percentual")."/100)) AS valor_novo                                                                           \n";                                                                                                                                              //
                } else {
                    $stSql .= "        , to_real(quantidade_registro) AS valor                                                                                                                                                      \n";     //
                    $stSql .= "        , to_real(quantidade_registro+(quantidade_registro*".$this->getDado("percentual")."/100)) AS valor_novo                                                                           \n";                                                                                                                                                  //
                }
            }
            $stSql .= "     FROM recuperarcontratopensionista('o,cgm,s,rf,sf,f,ef','".Sessao::getEntidade()."',".$this->getDado("inCodPeriodoMovimentacao").",'".$this->getDado("stTipoFiltro")."','".$this->getDado("stValoresFiltro")."','".Sessao::getExercicio()."') as contrato\n";
        } else {
            $stSql  = "   SELECT contrato.cod_contrato\n";
            $stSql .= "        , contrato.registro\n";
            $stSql .= "        , contrato.nom_cgm\n";
            $stSql .= "        , contrato.cod_padrao\n";
            $stSql .= "        , contrato.desc_orgao as orgao\n";
            $stSql .= "        , contrato.desc_regime_funcao as regime\n";
            $stSql .= "        , contrato.desc_sub_divisao_funcao as sub_divisao\n";
            $stSql .= "        , contrato.desc_funcao as funcao\n";
            $stSql .= "        , contrato.desc_especialidade_funcao as especialidade\n";
            if ($this->getDado('stTipoReajuste') == 'v') {/* valor absoluto */
                if ($this->getDado("stReajuste") == "p") {
                    $stSql .= "  , to_real(contrato.valor_padrao) as valor\n";
                    $stSql .= "  , to_real(".$this->getDado("nuValorNovo").") as valor_novo\n";
                    $stSql .= "  , to_real(contrato.salario) AS salario\n";
                    $stSql .= "  , to_real(".$this->getDado("nuValorNovo").") AS salario_novo\n";
                } else {
                    if ($this->getDado("stFixado") == "V") {
                        $stSql .= "        , to_real(valor_registro) AS valor\n";
                        $stSql .= "        , to_real(".$this->getDado("nuValorNovo").") AS valor_novo\n";
                    } else {
                        $stSql .= "        , to_real(quantidade_registro) AS valor\n";
                        $stSql .= "        , to_real(".$this->getDado("nuValorNovo").") AS valor_novo\n";
                    }
                }
            } else {/* reajuste por percentual */
                if ($this->getDado("stReajuste") == "p") {
                    $stSql .= "        , to_real(contrato.valor_padrao) AS valor\n";
                    $stSql .= "        , to_real(contrato.valor_padrao+(contrato.valor_padrao*".$this->getDado("percentual")."/100)) AS valor_novo      \n";
                    $stSql .= "        , to_real(contrato.salario) AS salario                                                                           \n";
                    $stSql .= "        , to_real(contrato.salario+(contrato.salario*".$this->getDado("percentual")."/100)) AS salario_novo              \n";
                } else {
                    if ($this->getDado("stFixado") == "V") {
                        $stSql .= "        , to_real(valor_registro) AS valor                                                                           \n";
                        $stSql .= "        , to_real(valor_registro+(valor_registro*".$this->getDado("percentual")."/100)) AS valor_novo                \n";                                                                                                                                              //
                    } else {
                        $stSql .= "        , to_real(quantidade_registro) AS valor                                                                      \n";
                        $stSql .= "        , to_real(quantidade_registro+(quantidade_registro*".$this->getDado("percentual")."/100)) AS valor_novo      \n";                                                                                                                                                  //
                    }
                }
            }
            $stSql .= "     FROM recuperarcontratoservidor('o,cgm,pp,rf,sf,f,ef,s','".Sessao::getEntidade()."',".$this->getDado("inCodPeriodoMovimentacao").",'".$this->getDado("stTipoFiltro")."','".$this->getDado("stValoresFiltro")."','".Sessao::getExercicio()."') as contrato\n";
        }
        if ($this->getDado('stReajuste') == "e") {
            if ($this->getDado("inCodConfiguracao") == 0) {
                $stSql .= "INNER JOIN (SELECT cod_contrato                                                                                      \n";
                $stSql .= "       , registro_evento_complementar.valor as valor_registro                                                        \n";
                $stSql .= "       , registro_evento_complementar.quantidade as quantidade_registro                                              \n";
                $stSql .= "    FROM folhapagamento.registro_evento_complementar                                       \n";
                $stSql .= "       , folhapagamento.ultimo_registro_evento_complementar                                \n";
                $stSql .= "   WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                \n";
                $stSql .= "     AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento                                 \n";
                $stSql .= "     AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao                     \n";
                $stSql .= "     AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp                                   \n";
                $stSql .= "     AND registro_evento_complementar.cod_evento = ".$this->getDado("inCodEvento")."                                 \n";
                $stSql .= "     AND registro_evento_complementar.cod_periodo_movimentacao = ".$this->getDado("inCodPeriodoMovimentacao").") as registro_evento  \n";
                $stSql .= "  ON contrato.cod_contrato = registro_evento.cod_contrato                                                            \n";
            }
            if ($this->getDado("inCodConfiguracao") == 1) {
                $stSql .= "INNER JOIN (SELECT cod_contrato                                                                               \n";
                $stSql .= "       , registro_evento.valor as valor_registro                                                        \n";
                $stSql .= "       , registro_evento.quantidade as quantidade_registro                                              \n";
                $stSql .= "    FROM folhapagamento.registro_evento_periodo                                \n";
                $stSql .= "       , folhapagamento.registro_evento                                        \n";
                $stSql .= "       , folhapagamento.ultimo_registro_evento                                 \n";
                $stSql .= "   WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                            \n";
                $stSql .= "     AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                             \n";
                $stSql .= "     AND registro_evento.proporcional = false                                                           \n";
                $stSql .= "     AND registro_evento.cod_evento = ".$this->getDado("inCodEvento")."                                 \n";
                if ($this->getDado("desdobramento")) {
                    $stSql .= "     AND NOT EXISTS ( SELECT 1                                                                                    \n";
                    $stSql .= "                        FROM folhapagamento.evento_calculado                             \n";
                    $stSql .= "                       WHERE evento_calculado.cod_evento = ultimo_registro_evento.cod_evento                      \n";
                    $stSql .= "                         AND evento_calculado.timestamp_registro = ultimo_registro_evento.timestamp               \n";
                    $stSql .= "                         AND evento_calculado.cod_registro = ultimo_registro_evento.cod_registro                  \n";
                    $stSql .= "                         AND (evento_calculado.desdobramento != '' OR evento_calculado.desdobramento != NULL)     \n";
                    $stSql .= "                     )                                                                                            \n";
                }
                $stSql .= "     AND cod_periodo_movimentacao = ".$this->getDado("inCodPeriodoMovimentacao").") as registro_evento  \n";
                $stSql .= "  ON contrato.cod_contrato = registro_evento.cod_contrato                                               \n";
            }
            if ($this->getDado("inCodConfiguracao") == 2) {
                $stSql .= "INNER JOIN (SELECT cod_contrato                                                                                                      \n";
                $stSql .= "       , registro_evento_ferias.valor as valor_registro                                                                        \n";
                $stSql .= "       , registro_evento_ferias.quantidade as quantidade_registro                                                              \n";
                $stSql .= "    FROM folhapagamento.registro_evento_ferias                                                        \n";
                $stSql .= "       , folhapagamento.ultimo_registro_evento_ferias                                                 \n";
                $stSql .= "   WHERE registro_evento_ferias.cod_registro = ultimo_registro_evento_ferias.cod_registro                                      \n";
                $stSql .= "     AND registro_evento_ferias.cod_evento = ultimo_registro_evento_ferias.cod_evento                                          \n";
                $stSql .= "     AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento                                    \n";
                $stSql .= "     AND registro_evento_ferias.timestamp = ultimo_registro_evento_ferias.timestamp                                            \n";
                $stSql .= "     AND registro_evento_ferias.cod_evento = ".$this->getDado("inCodEvento")."                                                 \n";
                $stSql .= "     AND registro_evento_ferias.cod_periodo_movimentacao = ".$this->getDado("inCodPeriodoMovimentacao").") as registro_evento  \n";
                $stSql .= "  ON contrato.cod_contrato = registro_evento.cod_contrato                                                                      \n";
            }
            if ($this->getDado("inCodConfiguracao") == 3) {
                $stSql .= "INNER JOIN (SELECT cod_contrato                                                                                                      \n";
                $stSql .= "       , registro_evento_decimo.valor as valor_registro                                                                        \n";
                $stSql .= "       , registro_evento_decimo.quantidade as quantidade_registro                                                              \n";
                $stSql .= "    FROM folhapagamento.registro_evento_decimo                                                        \n";
                $stSql .= "       , folhapagamento.ultimo_registro_evento_decimo                                                 \n";
                $stSql .= "   WHERE registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro                                      \n";
                $stSql .= "     AND registro_evento_decimo.cod_evento = ultimo_registro_evento_decimo.cod_evento                                          \n";
                $stSql .= "     AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento                                    \n";
                $stSql .= "     AND registro_evento_decimo.timestamp = ultimo_registro_evento_decimo.timestamp                                            \n";
                $stSql .= "     AND registro_evento_decimo.cod_evento = ".$this->getDado("inCodEvento")."                                                 \n";
                $stSql .= "     AND registro_evento_decimo.cod_periodo_movimentacao = ".$this->getDado("inCodPeriodoMovimentacao").") as registro_evento  \n";
                $stSql .= "  ON contrato.cod_contrato = registro_evento.cod_contrato                                                            \n";
            }
            if ($this->getDado("inCodConfiguracao") == 4) {
                $stSql .= "INNER JOIN (SELECT cod_contrato                                                                                                        \n";
                $stSql .= "       , registro_evento_rescisao.valor as valor_registro                                                                        \n";
                $stSql .= "       , registro_evento_rescisao.quantidade as quantidade_registro                                                              \n";
                $stSql .= "    FROM folhapagamento.registro_evento_rescisao                                                        \n";
                $stSql .= "       , folhapagamento.ultimo_registro_evento_rescisao                                                 \n";
                $stSql .= "   WHERE registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro                                    \n";
                $stSql .= "     AND registro_evento_rescisao.cod_evento = ultimo_registro_evento_rescisao.cod_evento                                        \n";
                $stSql .= "     AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento                                  \n";
                $stSql .= "     AND registro_evento_rescisao.timestamp = ultimo_registro_evento_rescisao.timestamp                                          \n";
                $stSql .= "     AND registro_evento_rescisao.cod_evento = ".$this->getDado("inCodEvento")."                                                 \n";
                $stSql .= "     AND registro_evento_rescisao.cod_periodo_movimentacao = ".$this->getDado("inCodPeriodoMovimentacao").") as registro_evento  \n";
                $stSql .= "  ON contrato.cod_contrato = registro_evento.cod_contrato                                                                        \n";
            }
        }
        $stSql .= "    WHERE recuperarSituacaoDoContrato(contrato.cod_contrato,".$this->getDado("inCodPeriodoMovimentacao").",'".Sessao::getEntidade()."') = '".$this->getDado("stSituacao")."'\n";

        return $stSql;
    }

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT padrao_padrao.*                                                     \n";
    $stSql .= "     , to_char(padrao_padrao.vigencia, 'dd/mm/yyyy') as vigencia_formatada \n";
    $stSql .= "  FROM folhapagamento.padrao_padrao         \n";
    $stSql .= "     , (SELECT cod_padrao                                            \n";
    $stSql .= "             , max(timestamp) as timestamp                           \n";
    $stSql .= "          FROM folhapagamento.padrao_padrao \n";
    $stSql .= "        GROUP BY cod_padrao) as max_padrao_padrao                    \n";
    $stSql .= " WHERE padrao_padrao.cod_padrao = max_padrao_padrao.cod_padrao       \n";
    $stSql .= "   AND padrao_padrao.timestamp = max_padrao_padrao.timestamp         \n";

    return $stSql;
}

}
