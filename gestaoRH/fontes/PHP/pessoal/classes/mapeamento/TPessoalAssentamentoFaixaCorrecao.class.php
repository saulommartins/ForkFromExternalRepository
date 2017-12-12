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
    * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_FAIXA_CORRECAO
    * Data de Criação: 10/08/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_FAIXA_CORRECAO
  * Data de Criação: 10/08/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoFaixaCorrecao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoFaixaCorrecao()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_faixa_correcao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_faixa,cod_assentamento,timestamp');

    $this->AddCampo('cod_faixa','integer',true,'',true,false);
    $this->AddCampo('cod_assentamento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('quant_meses','integer',true,'',false,false);
    $this->AddCampo('percentual_correcao','numeric',true,'5,2',false,false);
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
   $stSQL .="SELECT                                                         \n";
   $stSQL .="   ac.*                                                        \n";
   $stSQL .="FROM                                                           \n";
   $stSQL .="   pessoal.assentamento_faixa_correcao as ac,              \n";
   $stSQL .="   (SELECT cod_assentamento , max(timestamp) as timestamp      \n";
   $stSQL .="    FROM pessoal.assentamento_faixa_correcao               \n";
   $stSQL .="         GROUP BY cod_assentamento ) as ult                    \n";
   $stSQL .=" WHERE ac.cod_assentamento = ult.cod_assentamento              \n";
   $stSQL .="   AND ac.timestamp = ult.timestamp                            \n";

   return $stSQL;
}

}
