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
    * Classe de mapeamento da tabela pessoal.causa_afastamento
    * Data de Criação: 25/08/2014

    * @author Desenvolvedor: Franver Sarmento de Moraes

    $Id: TPessoalCausaAfastamentoMTE.class.php 59612 2014-09-02 12:00:51Z gelson $
    $Revision: 59612 $
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalCausaAfastamentoMTE extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TPessoalCausaAfastamentoMTE()
    {
        parent::Persistente();
        $this->setTabela("pessoal.causa_afastamento_mte");
    
        $this->setCampoCod('cod_causa_afastamento');
        $this->setComplementoChave('');
    
        $this->AddCampo('cod_causa_afastamento', 'varchar', true, '',  true, false);
        $this->AddCampo('nom_causa_afastamento', 'varchar', true, '', false, false);
    }
}
?>
