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
* Classe de Mapeamento para a tabela processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: TProtocoloProcesso.class.php 66642 2016-10-21 16:21:03Z diogo.zarpelon $

$Revision: 17315 $
$Name$
$Author: cassiano $
$Date: 2006-10-31 09:28:19 -0300 (Ter, 31 Out 2006) $

Casos de uso: uc-01.06.98
*/

include_once(TPRO."TProcesso.class.php");

class TProtocoloProcesso extends TProcesso
{
    public function TProtocoloProcesso()
    {
        parent::TProcesso();
    }

    public function mascararProcesso($inCodigoProcesso, $stExercicio)
    {
        $stMascara = SistemaLegado::pegaConfiguracao('mascara_processo',5);
        $arMAscara = preg_split('/[^a-zA-Z0-9]/', $stMascara );
        $chSeparador = substr($stMascara, strlen($arMAscara[0]),1 );

        return str_pad($inCodigoProcesso,strlen($arMAscara[0]),"0").$chSeparador.str_pad($stExercicio,strlen($arMAscara[1]),"0");
    }
}
