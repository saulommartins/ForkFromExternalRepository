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
    * Classe de regra de negócio para MONETARIO.CREDITO_CARTEIRA
    * Data de Criação: 07/02/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONCreditoContaCorrente.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.10
*/

/*

$Log$
Revision 1.2  2007/04/16 14:12:38  fabio
Bug #9078#

Revision 1.1  2007/02/08 10:35:52  cercato
alteracoes para o credito trabalhar com conta corrente.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONCreditoContaCorrente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONCreditoContaCorrente()
{
    parent::Persistente();
    $this->setTabela('monetario.credito_conta_corrente');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_especie,cod_genero,cod_natureza,cod_credito');

    $this->AddCampo('cod_credito','INTEGER',true,'',true,true);
    $this->AddCampo('cod_natureza','INTEGER',true,'',true,true);
    $this->AddCampo('cod_genero','INTEGER',true,'',true,true);
    $this->AddCampo('cod_especie','INTEGER',true,'',true,true);

    $this->AddCampo('cod_convenio','INTEGER',true,'',false,true);
    $this->AddCampo('cod_banco','INTEGER',true,'',false,true);
    $this->AddCampo('cod_agencia','INTEGER',true,'',false,true);
    $this->AddCampo('cod_conta_corrente','INTEGER',true,'',false,true);
}

} // fecha classe
