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
  * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_FAIXA_DESCONTO
  * Data de Criação: 03/02/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_FAIXA_DESCONTO
  * Data de Criação: 03/02/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoFaixaDesconto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoFaixaDesconto()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_faixa_desconto');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_faixa,cod_assentamento,timestamp');

    $this->AddCampo('cod_faixa','INTEGER',true,'',true,false);
    $this->AddCampo('cod_assentamento','INTEGER',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('valor_inicial','INTEGER',true,'',false,false);
    $this->AddCampo('valor_final','INTEGER',true,'',false,false);
    $this->AddCampo('percentual_desconto','numeric',true,'5.2',false,false);

}

function montaRecuperaRelacionamento()
{
$stSQL .="   SELECT                                                                    \n";
$stSQL .="           pafd.cod_faixa ,                                                  \n";
$stSQL .="           pafd.cod_assentamento ,                                           \n";
$stSQL .="           TO_CHAR(pafd.timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp , \n";
$stSQL .="           pafd.valor_inicial ,                                              \n";
$stSQL .="           pafd.valor_final ,                                                \n";
$stSQL .="           pafd.percentual_desconto                                          \n";
$stSQL .="   FROM                                                                      \n";
$stSQL .="           pessoal.assentamento_faixa_desconto pafd,                     \n";
$stSQL .="          (select                                                            \n";
$stSQL .="                   cod_assentamento,                                         \n";
$stSQL .="                   max(timestamp) as timestamp                               \n";
$stSQL .="              from                                                           \n";
$stSQL .="                   pessoal.assentamento                                  \n";
$stSQL .="           group by cod_assentamento) as PA                                  \n";
$stSQL .="   WHERE                                                                     \n";
$stSQL .="       pafd.cod_assentamento = pa.cod_assentamento and                       \n";
$stSQL .="       pafd.timestamp        = pa.timestamp                                  \n";

return $stSQL;
}

}
