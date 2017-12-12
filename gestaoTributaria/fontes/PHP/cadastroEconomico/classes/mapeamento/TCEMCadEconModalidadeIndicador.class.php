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
  * Classe de mapeamento da tabela ECONOMICO.CAD_ECON_MODALIDADE_INDICADOR
  * Data de Criação: 18/10/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fabio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCadEconModalidadeIndicador.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.1  2006/11/08 10:34:36  fabio
alteração do uc_05.02.13

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCEMCadEconModalidadeIndicador extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCadEconModalidadeIndicador()
{
    parent::Persistente();
    $this->setTabela('economico.cad_econ_modalidade_indicador');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_modalidade,cod_atividade,inscricao_economica,ocorrencia_atividade,dt_inicio,cod_indicador');

    $this->AddCampo('cod_modalidade'      ,'integer',true ,'',true ,true );
    $this->AddCampo('cod_atividade'       ,'integer',true ,'',true ,true );
    $this->AddCampo('inscricao_economica' ,'integer',true ,'',true ,true );
    $this->AddCampo('ocorrencia_atividade','integer',true ,'',true ,true );
    $this->AddCampo('dt_inicio'           ,'date'   ,true ,'',true ,false);
    $this->AddCampo('cod_indicador'       ,'integer',true ,'',true ,true );
}

}
