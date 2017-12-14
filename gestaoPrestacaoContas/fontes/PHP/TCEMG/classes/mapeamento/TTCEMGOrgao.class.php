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
    * Classe de mapeamento da tabela TCEMG.ORGAO
    * Data de Criação: 14/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

class TTCEMGOrgao extends Persistente
{
    public function TTCEMGOrgao()
    {
        parent::Persistente();
        $this->setTabela('tcemg.orgao');

        $this->setCampoCod('num_orgao');
        $this->setComplementoChave('exercicio');

        $this->AddCampo( 'num_orgao' ,'integer' ,true, ''   ,true ,true  );
        $this->AddCampo( 'exercicio','varchar' ,true, '4' ,true,true );
        $this->AddCampo( 'nom_orgao','varchar' ,true, '' ,false,true );
    }
    
    public function __destruct(){}

}

?>
