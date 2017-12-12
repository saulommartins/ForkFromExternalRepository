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
    * Classe de mapeamento da tabela pessoal.assentamento_licenca_premio
    * Data de Criação: 17/10/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-17 11:36:19 -0200 (Qua, 17 Out 2007) $

    * Casos de uso: uc-04.04.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.assentamento_licenca_premio
  * Data de Criação: 17/10/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoLicencaPremio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoLicencaPremio()
{
    parent::Persistente();
    $this->setTabela("pessoal.assentamento_licenca_premio");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento_gerado,timestamp');

    $this->AddCampo('cod_assentamento_gerado','integer'  ,true  ,'',true,'TPessoalAssentamentoGerado');
    $this->AddCampo('timestamp'              ,'timestamp',true  ,'',true,'TPessoalAssentamentoGerado');
    $this->AddCampo('dt_inicial'             ,'date'     ,true  ,'',false,false);
    $this->AddCampo('dt_final'               ,'date'     ,true  ,'',false,false);

}
}
?>
