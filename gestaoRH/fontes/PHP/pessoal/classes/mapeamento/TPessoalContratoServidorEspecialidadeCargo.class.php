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
  * Classe de mapeamento da tabela PESSOAL.Contrato_Servidor_Especialiade_Cargo
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.Contrato_Servidor_Especialiade_Cargo
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorEspecialidadeCargo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorEspecialidadeCargo()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_especialidade_cargo');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_especialidade');

    $this->AddCampo('cod_contrato','integer',true,'',true,true);
    $this->AddCampo('cod_especialidade','integer',true,'',true,true);

}

}
