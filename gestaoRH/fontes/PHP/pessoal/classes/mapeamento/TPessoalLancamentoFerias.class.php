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
    * Classe de mapeamento da tabela pessoal.lancamento_ferias
    * Data de Criação: 23/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.04.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.lancamento_ferias
  * Data de Criação: 23/06/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalLancamentoFerias extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalLancamentoFerias()
{
    parent::Persistente();
    $this->setTabela("pessoal.lancamento_ferias");

    $this->setCampoCod('cod_ferias');
    $this->setComplementoChave('');

    $this->AddCampo('cod_ferias'            ,'integer'  ,true   ,''    ,true   ,'TPessoalFerias');
    $this->AddCampo('dt_inicio'             ,'date'     ,true   ,''    ,false  ,false);
    $this->AddCampo('dt_fim'                ,'date'     ,true   ,''    ,false  ,false);
    $this->AddCampo('dt_retorno'            ,'date'     ,true   ,''    ,false  ,false);
    $this->AddCampo('mes_competencia'       ,'char'     ,true   ,'2'   ,false  ,false);
    $this->AddCampo('ano_competencia'       ,'char'     ,true   ,'4'   ,false  ,false);
    $this->AddCampo('pagar_13'              ,'boolean'  ,true   ,''    ,false  ,false);
    $this->AddCampo('cod_tipo'              ,'integer'  ,true   ,''     ,false  ,'TFolhaPagamentoTipoFolha'             );
}

function recuperaLancamentoFerias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaLancamentoFerias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaLancamentoFerias()
{
    $stSql .= "   SELECT lancamento_ferias.*                                                      \n";
    $stSql .= "        , to_char(lancamento_ferias.dt_inicio,'dd/mm/yyyy') as dt_inicio_formatado \n";
    $stSql .= "        , to_char(lancamento_ferias.dt_fim,'dd/mm/yyyy') as dt_fim_formatado       \n";
    $stSql .= "        , lote_ferias_lote.cod_lote                                                \n";
    $stSql .= "        , ferias.cod_contrato                                              \n";
    $stSql .= "     FROM pessoal.lancamento_ferias               \n";
    $stSql .= "LEFT JOIN pessoal.lote_ferias_lote                \n";
    $stSql .= "       ON lote_ferias_lote.cod_ferias = lancamento_ferias.cod_ferias       \n";
    $stSql .= "        , pessoal.ferias                          \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor      \n";
    $stSql .= "        , pessoal.servidor                        \n";
    $stSql .= "    WHERE ferias.cod_ferias = lancamento_ferias.cod_ferias                 \n";
    $stSql .= "      AND ferias.cod_contrato = servidor_contrato_servidor.cod_contrato    \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor  \n";

    return $stSql;
}

}
