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
    * Classe de mapeamento 
    * Data de Criação: 12/11/2014

    * @author Analista: Silvia Martins
    * @author Desenvolvedor: Evandro Melos
    *
    * $Id: TTCEALIdentificadorAcao.class.php 60752 2014-11-13 13:10:31Z evandro $
*/

class TTCEALIdentificadorAcao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALIdentificadorAcao()
    {
        parent::Persistente();

        $this->setTabela('tceal.identificador_acao');

        $this->setCampoCod('cod_identificador');

        $this->AddCampo('cod_identificador' , 'integer'  , true, ''  , true , true );
        $this->AddCampo('descricao'         , 'char'     , true, '60', false, true );
        
    }
    
 } // end of class
