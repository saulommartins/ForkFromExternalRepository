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
    * Arquivo de popup de busca de CGM
    * Data de Criação: 27/02/2003

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.01
*/

/*
$Log$
Revision 1.3  2006/07/06 14:04:38  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:09:20  diego

*/

include_once ( CLA_SELECT_MULTIPLO );

class ISelectMultiploAlmoxarifadoAlmoxarife extends SelectMultiplo
{
    public function ISelectMultiploAlmoxarifadoAlmoxarife()
    {
        parent::SelectMultiplo();

        include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php" );
        $obRAlmoxarifadoAlmoxarife  = new RAlmoxarifadoAlmoxarife();
        $rsAlmoxarifados            = new Recordset;
        $rsRecordSet                = new RecordSet;

        $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM(Sessao::read('numCgm'));
        $obRAlmoxarifadoAlmoxarife->listarPermissao($rsAlmoxarifados,"",false);
        $obRAlmoxarifadoAlmoxarife->consultar();
        $inCodigo = $obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->getCodigo();
        if ( !empty($inCodigo) ) {
            $arRecordSet[0]['codigo'] = $inCodigo;
            $arRecordSet[0]['nom_a']  = $obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->obRCGMAlmoxarifado->getNomCGM();
            $rsRecordSet->preenche( $arRecordSet );
        }

        $this->setName       ('inCodAlmoxarifado');
        $this->setRotulo     ( "Almoxarifado" );
        $this->setTitle      ( "Selecione os almoxarifados que o almoxarife possui permissão." );
        $this->SetNomeLista1 ('inCodAlmoxarifadoDisponivel');
        $this->setCampoId1   ( 'codigo' );
        $this->setCampoDesc1 ( '[codigo]-[nom_a]' );
        $this->SetRecord1    ( $rsAlmoxarifados );
        $this->SetNomeLista2 ('inCodAlmoxarifado');
        $this->setCampoId2   ('codigo');
        $this->setCampoDesc2 ('[codigo]-[nom_a]');
        $this->SetRecord2    ( $rsRecordSet );
    }
}
?>
