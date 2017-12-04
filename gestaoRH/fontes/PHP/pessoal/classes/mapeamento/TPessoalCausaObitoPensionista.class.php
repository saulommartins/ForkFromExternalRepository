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
    * Classe de mapeamento da tabela pessoal.causa_obito_pensionista
    * * Data de Criação: 01/07/2013

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalCausaObitoPensionista extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCausaObitoPensionista()
{
    parent::Persistente();
    $this->setTabela("pessoal.causa_obito_pensionista");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato');

    $this->AddCampo('cod_contrato'      ,'integer',false ,''    ,true,'TPessoalContratoPensionistaCasoCausa');
    $this->AddCampo('num_certidao_obito','varchar',false ,'10'  ,false,false);
    $this->AddCampo('causa_mortis'      ,'varchar',false ,'200' ,false,false);

}

}
