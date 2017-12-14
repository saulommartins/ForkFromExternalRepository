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
  * Classe de mapeamento da tabela pessoal.cargo_requisito
  * Data de Criação: 19/10/2012

  * @author Analista:
  * @author Desenvolvedor: Davi Ritter Aroldi

  * @package URBEM
  * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.cargo_requisito
  * Data de Criação: 19/10/2012

  * @author Analista:
  * @author Desenvolvedor: Davi Ritter Aroldi

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCargoRequisito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPessoalCargoRequisito()
    {
        parent::Persistente();
        $this->setTabela('pessoal.cargo_requisito');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_cargo,cod_requisito');

        $this->AddCampo('cod_requisito'     , 'integer',  true,   '' ,  true, 'TPessoalRequisito');
        $this->AddCampo('cod_cargo'         , 'integer',  true,   '' ,  true, 'TPessoalCargo');

    }
}
