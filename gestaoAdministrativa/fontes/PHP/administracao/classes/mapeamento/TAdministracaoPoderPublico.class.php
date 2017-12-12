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
* Classe de mapeamento para administracao.poder_publico
* Data de Criação: 30/07/2013

* @author Desenvolvedor: Carolina Schwaab Marcal

$Revision:  $
$Name$
$Author:$
$Date:  $

Casos de uso:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TAdministracaoPoderPublico extends Persistente
{
    public function TAdministracaoPoderPublico()
    {
        parent::Persistente();
        $this->setTabela('administracao.poder_publico');
        $this->setCampoCod('cod_poder');
        $this->setCampoCod('nome');

        $this->AddCampo('cod_poder', 'integer', true, '', true,  false);
        $this->AddCampo('nome', 'varchar', true, 80, false, false);
    }
}
