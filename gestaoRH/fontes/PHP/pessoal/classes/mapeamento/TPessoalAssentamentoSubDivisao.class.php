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
  * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_SUB_DIVISAO
  * Data de Criação: 03/06/2005

  * @author Analista: Leandro Oliveria
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
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_SUB_DIVISAO
  * Data de Criação: 03/06/2005

  * @author Analista: Leandro Oliveria
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoSubDivisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoSubDivisao()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_sub_divisao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_sub_divisao,cod_assentamento,timestamp');

    $this->AddCampo('cod_assentamento','integer',true,'',true,true);
    $this->AddCampo('cod_sub_divisao','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('vigencia','date',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSQL .= "    SELECT                                                              \n";
    $stSQL .= "     pr.descricao as nom_regime,                                        \n";
    $stSQL .= "     pr.cod_regime,                                                     \n";
    $stSQL .= "     psd.descricao as nom_sub_divisao,                                  \n";
    $stSQL .= "     psd.cod_sub_divisao,                                               \n";
    $stSQL .= "     psd.cod_regime                                                     \n";
    $stSQL .= " FROM                                                                   \n";
    $stSQL .= "    pessoal.sub_divisao                as psd,                      \n";
    $stSQL .= "    pessoal.regime                      as pr,                      \n";
    $stSQL .= "   (select                                                              \n";
    $stSQL .= "         cod_assentamento, max(timestamp) as timestamp                  \n";
    $stSQL .= "     from                                                               \n";
    $stSQL .= "         pessoal.assentamento                                       \n";
    $stSQL .= "     group by cod_assentamento) as pa,                                  \n";
    $stSQL .= "    pessoal.assentamento_sub_divisao  as pasd                       \n";
    $stSQL .= " WHERE                                                                  \n";
    $stSQL .= "       pr.cod_regime         = psd.cod_regime      and                  \n";
    $stSQL .= "       pasd.cod_sub_divisao  = psd.cod_sub_divisao and                  \n";
    $stSQL .= "       pasd.cod_assentamento = pa.cod_assentamento and                  \n";
    $stSQL .= "       pasd.timestamp        = pa.timestamp                             \n";

    return $stSQL;
 }
}
