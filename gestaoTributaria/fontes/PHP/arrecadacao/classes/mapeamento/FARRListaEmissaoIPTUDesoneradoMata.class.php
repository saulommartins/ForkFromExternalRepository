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
    * Classe de mapeamento da função ARRECADACAO.buscaLancamentosIE()
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FARRListaEmissao.class.php 45716 2011-08-23 14:47:34Z davi.aroldi $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.5  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 12/05/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento
*/
class FARRListaEmissaoIPTUDesoneradoMata extends Persistente
{
    public $inExercicio       ;
    public $inCodGrupoCredito ;
    public $inCodIIInicial ;
    public $inCodIIFinal   ;

/**
    * Método Construtor
    * @access Private
*/
function FARRListaEmissaoIPTUDesoneradoMata()
{
    parent::Persistente();
    $this->inExercicio    = '0';
    $this->inCodGrupo     = '0';
    $this->inCodIIInicial = '0';
    $this->inCodIIFinal   = '0';
}

function executaFuncao(&$rsRecordset, $stParametros,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaFuncao();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaExecutaFuncao()
{
    $stSql = " SELECT '0'::varchar as numeracao
                    , exercicio as exercicio_calculo
                    , '2011'::varchar as exercicio
                    , 'f'::varchar as impresso
                    , '0'::varchar as cod_convenio
                    , '0'::varchar as cod_carteira
                    , '0'::varchar as convenio_atual
                    , '0'::varchar as carteira_atual
                    , '0'::varchar as cod_parcela
                    , '0'::varchar as nr_parcela
                    , 'Desonerado'::varchar as info_parcela
                    , '0000-00-00'::varchar as vencimento_parcela
                    , '00/00/0000'::varchar as vencimento_parcela_br
                    , '0000-00-00'::varchar as vencimento_original
                    , '00/00/0000'::varchar as vencimento_original_br
                    , '0000-00-00'::varchar as vencimento_parcela
                    , '0000-00-00'::varchar as vencimento_parcela
                    , '0000-00-00'::varchar as vencimento_parcela
                    , valor_parcela
                    , cod_lancamento
                    , vencimento_lancamento
                    , valor_lancamento
                    , numcgm
                    , nom_cgm
                    , vinculo
                    , id_vinculo
                    , chave_vinculo
                    , inscricao
                 FROM (

                            SELECT  calculo.exercicio
                                 , imovel_valor_desonerado.valor_parcela AS valor_parcela
                                 , lancamento.cod_lancamento
                                 , lancamento.vencimento as vencimento_lancamento
                                 , lancamento.valor as valor_lancamento
                                 , CAST((SELECT array_to_string( ARRAY( SELECT numcgm FROM sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = calculo.cod_calculo)), '/' ) ) AS VARCHAR) AS numcgm
                                 , CAST((SELECT array_to_string( ARRAY( SELECT nom_cgm FROM sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = calculo.cod_calculo)), '/' ) ) AS VARCHAR) AS nom_cgm
                                 , arrecadacao.buscaVinculoLancamentoSemExercicio ( lancamento.cod_lancamento )::varchar as vinculo
                                 , arrecadacao.buscaIdVinculo(lancamento.cod_lancamento, calculo.exercicio::integer )::varchar as id_vinculo
                                 , md5(arrecadacao.buscaVinculoLancamentoSemExercicio ( lancamento.cod_lancamento ))::varchar as chave_vinculo
                                 , arrecadacao.buscaInscricaoLancamento ( lancamento.cod_lancamento )::integer as inscricao

                              FROM arrecadacao.imovel_calculo
                        INNER JOIN arrecadacao.calculo
                                ON calculo.cod_calculo = imovel_calculo.cod_calculo

                        INNER JOIN arrecadacao.calculo_grupo_credito
                                ON calculo_grupo_credito.cod_calculo   = calculo.cod_calculo
                               AND calculo_grupo_credito.ano_exercicio = calculo.exercicio

                        INNER JOIN arrecadacao.grupo_credito
                                ON grupo_credito.cod_grupo     = calculo_grupo_credito.cod_grupo
                               AND grupo_credito.ano_exercicio = calculo_grupo_credito.ano_exercicio

                        INNER JOIN arrecadacao.lancamento_calculo
                                ON lancamento_calculo.cod_calculo = calculo.cod_calculo

                        INNER JOIN arrecadacao.lancamento
                                ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento

                        INNER JOIN monetario.credito
                                ON credito.cod_credito  = calculo.cod_credito
                               AND credito.cod_especie  = calculo.cod_especie
                               AND credito.cod_genero   = calculo.cod_genero
                               AND credito.cod_natureza = calculo.cod_natureza

                         LEFT JOIN monetario.carteira
                                ON carteira.cod_convenio = credito.cod_convenio

                        INNER JOIN arrecadacao.desoneracao
                                ON desoneracao.cod_credito  = credito.cod_credito
                               AND desoneracao.cod_especie  = credito.cod_especie
                               AND desoneracao.cod_genero   = credito.cod_genero
                               AND desoneracao.cod_natureza = credito.cod_natureza

                        INNER JOIN arrecadacao.desonerado
                                ON desonerado.cod_desoneracao = desoneracao.cod_desoneracao

                        INNER JOIN arrecadacao.lancamento_usa_desoneracao
                                ON lancamento_usa_desoneracao.cod_desoneracao = desonerado.cod_desoneracao
                               AND lancamento_usa_desoneracao.numcgm 	   = desonerado.numcgm
                               AND lancamento_usa_desoneracao.ocorrencia 	   = desonerado.ocorrencia
                               AND lancamento_usa_desoneracao.cod_lancamento  = lancamento_calculo.cod_lancamento
                               AND lancamento_usa_desoneracao.cod_calculo     = lancamento_calculo.cod_calculo

                        INNER JOIN arrecadacao.desonerado_imovel
                                ON desonerado_imovel.cod_desoneracao = desonerado.cod_desoneracao
                               AND desonerado_imovel.numcgm 	  = desonerado.numcgm
                               AND desonerado_imovel.ocorrencia 	  = desonerado.ocorrencia

                        INNER JOIN (SELECT SUM(calculo.valor) AS valor_parcela, imovel_calculo.inscricao_municipal
                              FROM arrecadacao.calculo

                        INNER JOIN arrecadacao.imovel_calculo
                                ON imovel_calculo.cod_calculo = calculo.cod_calculo

                        INNER JOIN arrecadacao.calculo_grupo_credito
                                ON calculo_grupo_credito.cod_calculo = calculo.cod_calculo

                         INNER JOIN arrecadacao.grupo_credito
                                 ON grupo_credito.cod_grupo = calculo_grupo_credito.cod_grupo
                                AND grupo_credito.cod_grupo =  ".$this->inCodGrupo."
                                AND grupo_credito.ano_exercicio =  '".$this->inExercicio."'

                              WHERE imovel_calculo.inscricao_municipal BETWEEN ".$this->inCodIIInicial." AND ".$this->inCodIIFinal."
                                AND calculo.ativo = TRUE

                           GROUP BY imovel_calculo.inscricao_municipal
                       ) AS imovel_valor_desonerado

                      ON imovel_valor_desonerado.inscricao_municipal = imovel_calculo.inscricao_municipal

               LEFT JOIN imobiliario.transferencia_imovel
                      ON transferencia_imovel.inscricao_municipal = imovel_calculo.inscricao_municipal
                    -- AND transferencia_imovel.cod_natureza = imovel_calculo.cod_natureza
                     AND transferencia_imovel.dt_cadastro = imovel_calculo.timestamp

               LEFT JOIN imobiliario.transferencia_adquirente
                      ON transferencia_adquirente.cod_transferencia = transferencia_imovel.cod_transferencia

                   WHERE calculo.exercicio = '".$this->inExercicio."'
                     AND imovel_calculo.inscricao_municipal BETWEEN ".$this->inCodIIInicial." AND ".$this->inCodIIFinal."
                     AND grupo_credito.cod_grupo =  ".$this->inCodGrupo."

                GROUP BY imovel_valor_desonerado.valor_parcela
                       , lancamento.cod_lancamento
                       , lancamento.vencimento
                       , lancamento.valor
                       , calculo.cod_calculo
                       , calculo.exercicio

                ORDER BY lancamento.cod_lancamento DESC
              ) AS tabela

       GROUP BY exercicio
              , valor_parcela
              , cod_lancamento
              , vencimento_lancamento
              , valor_lancamento
              , numcgm
              , nom_cgm
              , vinculo
              , id_vinculo
              , chave_vinculo
              , inscricao";

return $stSql;
}

}
?>
