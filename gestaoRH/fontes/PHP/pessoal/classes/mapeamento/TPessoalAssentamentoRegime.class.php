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
  * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_REGIME
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

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
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_REGIME
  * Data de Criação: 03/02/2005
*/
class TPessoalAssentamentoRegime extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoRegime()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_regime');

    $this->setCampoCod('');
//    $this->setComplementoChave('cod_assentamento,cod_regime');
    $this->setComplementoChave('cod_assentamento');

    $this->AddCampo('cod_assentamento','INTEGER',true,'',true,true);
    $this->AddCampo('cod_regime','INTEGER',true,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSQL .= " SELECT                                       \n";
    $stSQL .= "     PAR.cod_regime                           \n";
    $stSQL .= " FROM                                         \n";
    $stSQL .= "    pessoal.assentamento as PA,           \n";
    $stSQL .= "    pessoal.assentamento_regime as PAR    \n";
    $stSQL .= " WHERE                                        \n";
    $stSQL .= "     PA.cod_assentamento = PAR.cod_assentamento\n";

    return $stSQL;
}

}
