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
* Classe de mapeamento para PESSOAL.CASO_CAUSA_SUB_DIVISAO
* Data de Criação: 09/05/2005

* @author Analista: Leandro OLiveira
* @author Desenvolvedor: Vandré Miguel Ramos

* @package URBEM
* @subpackage Mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CASO_CAUSA_SUB_DIVISAO
  * Data de Criação: 09/05/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCasoCausaSubDivisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCasoCausaSubDivisao()
{
    parent::Persistente();
    $this->setTabela('pessoal.caso_causa_sub_divisao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_sub_divisao,cod_caso_causa');

    $this->AddCampo('cod_sub_divisao','integer',true,'',true,true);
    $this->AddCampo('cod_caso_causa','integer',true,'',true,true);

}
function montaRecuperaRelacionamento()
{
    $stSQL .= " SELECT                                                \n";
    $stSQL .= "     pr.descricao as nom_regime,                       \n";
    $stSQL .= "     pr.cod_regime,                                    \n";
    $stSQL .= "     psd.descricao as nom_sub_divisao,                 \n";
    $stSQL .= "     psd.cod_sub_divisao,                              \n";
    $stSQL .= "     psd.cod_regime                                    \n";
    $stSQL .= " FROM                                                  \n";
    $stSQL .= "    pessoal.sub_divisao                  as psd,   \n";
    $stSQL .= "    pessoal.caso_causa_sub_divisao     as pccsd,   \n";
    $stSQL .= "    pessoal.regime                        as pr,   \n";
    $stSQL .= "    pessoal.caso_causa                    as pcc   \n";
    $stSQL .= " WHERE                                                 \n";
    $stSQL .= "     pr.cod_regime = psd.cod_regime   and              \n";
    $stSQL .= "     pccsd.cod_sub_divisao  = psd.cod_sub_divisao and  \n";
    $stSQL .= "     pccsd.cod_caso_causa   = pcc.cod_caso_causa       \n";

    return $stSQL;
}

}
