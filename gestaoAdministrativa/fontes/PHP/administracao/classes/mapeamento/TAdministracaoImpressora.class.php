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
* Classe de mapeamento para administracao.impressora
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

Casos de uso: uc-01.03.92

$Id: TAdministracaoImpressora.class.php 59612 2014-09-02 12:00:51Z gelson $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  SW_IMPRESSORA
  * Data de Criação: 05/08/2004

  * @author Analista: Ricardo
  * @author Desenvolvedor: Cassiano Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoImpressora extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoImpressora()
{
    parent::Persistente();
    $this->setTabela('administracao.impressora');

    $this->setCampoCod('cod_impressora');
    $this->setComplementoChave('');

    $this->AddCampo('cod_impressora', 'integer','true','', true, true );
    $this->AddCampo('nom_impressora', 'varchar','true','30', false, false );
    $this->AddCampo('cod_orgao', 'integer','true','', false, true );
    $this->AddCampo('cod_local', 'integer','true','', false, true );
    $this->AddCampo('fila_impressao', 'varchar','true','15', false, false);

    # Estrutura antiga do Organograma.

    # $this->AddCampo('cod_unidade', 'integer','true','',false, true );
    # $this->AddCampo('cod_departamento', 'integer','true','',false, true );
    # $this->AddCampo('cod_setor', 'integer','true','', false, true );

}
}
?>
