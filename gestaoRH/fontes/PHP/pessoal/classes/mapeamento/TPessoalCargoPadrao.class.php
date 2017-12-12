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
  * Classe de mapeamento da tabela PESSOAL.CARGO_PADRAO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CARGO_PADRAO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCargoPadrao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCargoPadrao()
{
    parent::Persistente();
    $this->setTabela('pessoal.cargo_padrao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_cargo, cod_padrao, timestamp_padrao');

    $this->AddCampo('cod_cargo'       , 'integer'  , true , '', true, true );
    $this->AddCampo('cod_padrao'      , 'integer'  , true , '', true, true );
    $this->AddCampo('timestamp'       , 'timestamp', false, '', true, false);
}

function montaRecuperaRelacionamento()
{
    $stSQL .= " SELECT                                                      \n";
    $stSQL .= "     PCP.cod_padrao,                                         \n";
    $stSQL .= "     FP.horas_mensais,                                       \n";
    $stSQL .= "     FP.horas_semanais,                                      \n";
    $stSQL .= "     FPP.valor,                                              \n";
    $stSQL .= "     to_char(FPP.vigencia,'dd/mm/yyyy') as vigencia          \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "    pessoal.cargo as PC,                                     \n";
    $stSQL .= "    pessoal.cargo_padrao   as PCP,                           \n";
    $stSQL .= "    (SELECT cod_cargo,max(timestamp) as timestamp            \n";
    $stSQL .= "    FROM pessoal.cargo_padrao                                \n";
    $stSQL .= "    GROUP BY cod_cargo) as max_cargo_padrao,                 \n";
    $stSQL .= "      folhapagamento.padrao   as FP,                         \n";
    $stSQL .= "      folhapagamento.padrao_padrao   as FPP,                 \n";
    $stSQL .= "    (SELECT cod_padrao,max(timestamp) as timestamp           \n";
    $stSQL .= "    FROM folhapagamento.padrao_padrao                        \n";
    $stSQL .= "    GROUP BY cod_padrao) as max_padrao_padrao                \n";
    $stSQL .= " WHERE                                                       \n";
    $stSQL .= "     PC.cod_cargo  = PCP.cod_cargo                           \n";
    $stSQL .= "     AND FP.cod_padrao = PCP.cod_padrao                      \n";
    $stSQL .= "     AND FPP.cod_padrao = FP.cod_padrao                      \n";
    $stSQL .= "     AND FPP.cod_padrao = max_padrao_padrao.cod_padrao       \n";
    $stSQL .= "     AND FPP.timestamp  = max_padrao_padrao.timestamp        \n";
    $stSQL .= "     AND PCP.cod_cargo  = max_cargo_padrao.cod_cargo         \n";
    $stSQL .= "     AND PCP.timestamp  = max_cargo_padrao.timestamp         \n";

    return $stSQL;
}

}
